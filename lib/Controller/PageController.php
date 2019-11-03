<?php
/**
 * Nextcloud - cospend
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Julien Veyssier <eneiluj@posteo.net>
 * @copyright Julien Veyssier 2019
 */

namespace OCA\Cospend\Controller;

use OCP\App\IAppManager;

use OCP\IURLGenerator;
use OCP\IConfig;
use \OCP\IL10N;

use OCP\AppFramework\Http;
use OCP\AppFramework\Http\RedirectResponse;

use OCP\AppFramework\Http\ContentSecurityPolicy;

use OCP\IRequest;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;
use OCP\AppFramework\ApiController;
use OCP\Constants;
use OCP\Share;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IUserManager;
use OCP\Share\IManager;
use OCP\IServerContainer;
use OCP\IGroupManager;
use OCP\ILogger;
use OCA\Cospend\Db\BillMapper;
use OCA\Cospend\Db\ProjectMapper;
use OCA\Cospend\Service\ProjectService;

function endswith($string, $test) {
    $strlen = strlen($string);
    $testlen = strlen($test);
    if ($testlen > $strlen) return false;
    return substr_compare($string, $test, $strlen - $testlen, $testlen) === 0;
}

class PageController extends ApiController {

    private $userId;
    private $userfolder;
    private $config;
    private $appVersion;
    private $shareManager;
    private $userManager;
    private $groupManager;
    private $dbconnection;
    private $dbtype;
    private $dbdblquotes;
    private $defaultDeviceId;
    private $trans;
    private $logger;
    protected $appName;

    public function __construct($AppName,
                                IRequest $request,
                                IServerContainer $serverContainer,
                                IConfig $config,
                                IManager $shareManager,
                                IAppManager $appManager,
                                IUserManager $userManager,
                                IGroupManager $groupManager,
                                IL10N $trans,
                                ILogger $logger,
                                BillMapper $billMapper,
                                ProjectMapper $projectMapper,
                                ProjectService $projectService,
                                $UserId){
        parent::__construct($AppName, $request,
                            'PUT, POST, GET, DELETE, PATCH, OPTIONS',
                            'Authorization, Content-Type, Accept',
                            1728000);
        $this->logger = $logger;
        $this->appName = $AppName;
        $this->billMapper = $billMapper;
        $this->projectMapper = $projectMapper;
        $this->projectService = $projectService;
        $this->appVersion = $config->getAppValue('cospend', 'installed_version');
        $this->userId = $UserId;
        $this->userManager = $userManager;
        $this->groupManager = $groupManager;
        $this->trans = $trans;
        $this->dbtype = $config->getSystemValue('dbtype');
        // IConfig object
        $this->config = $config;

        if ($this->dbtype === 'pgsql'){
            $this->dbdblquotes = '"';
        }
        else{
            $this->dbdblquotes = '`';
        }
        $this->dbconnection = \OC::$server->getDatabaseConnection();
        if ($UserId !== '' and $serverContainer !== null){
            // path of user files folder relative to DATA folder
            $this->userfolder = $serverContainer->getUserFolder($UserId);
        }
        $this->shareManager = $shareManager;
    }

    /*
     * quote and choose string escape function depending on database used
     */
    private function db_quote_escape_string($str){
        return $this->dbconnection->quote($str);
    }

    /**
     * Welcome page
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function index() {
        // PARAMS to view
        $params = [
            'projectid'=>'',
            'password'=>'',
            'username'=>$this->userId,
            'cospend_version'=>$this->appVersion
        ];
        $response = new TemplateResponse('cospend', 'main', $params);
        $csp = new ContentSecurityPolicy();
        $csp->addAllowedImageDomain('*')
            ->addAllowedMediaDomain('*')
            //->addAllowedChildSrcDomain('*')
            ->addAllowedFrameDomain('*')
            ->addAllowedWorkerSrcDomain('*')
            //->allowInlineScript(true)
            //->allowEvalScript(true)
            ->addAllowedObjectDomain('*')
            ->addAllowedScriptDomain('*')
            ->addAllowedConnectDomain('*');
        $response->setContentSecurityPolicy($csp);
        return $response;
    }


    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     */
    public function pubLoginProject($projectid) {
        // PARAMS to view
        $params = [
            'projectid'=>$projectid,
            'wrong'=>false,
            'cospend_version'=>$this->appVersion
        ];
        $response = new TemplateResponse('cospend', 'login', $params);
        $csp = new ContentSecurityPolicy();
        $csp->addAllowedImageDomain('*')
            ->addAllowedMediaDomain('*')
            //->addAllowedChildSrcDomain('*')
            ->addAllowedFrameDomain('*')
            ->addAllowedWorkerSrcDomain('*')
            ->addAllowedObjectDomain('*')
            ->addAllowedScriptDomain('*')
            ->addAllowedConnectDomain('*');
        $response->setContentSecurityPolicy($csp);
        return $response;
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     */
    public function pubLogin() {
        // PARAMS to view
        $params = [
            'wrong'=>false,
            'cospend_version'=>$this->appVersion
        ];
        $response = new TemplateResponse('cospend', 'login', $params);
        $csp = new ContentSecurityPolicy();
        $csp->addAllowedImageDomain('*')
            ->addAllowedMediaDomain('*')
            //->addAllowedChildSrcDomain('*')
            ->addAllowedFrameDomain('*')
            ->addAllowedWorkerSrcDomain('*')
            ->addAllowedObjectDomain('*')
            ->addAllowedScriptDomain('*')
            ->addAllowedConnectDomain('*');
        $response->setContentSecurityPolicy($csp);
        return $response;
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     */
    public function pubProject($projectid, $password) {
        if ($this->checkLogin($projectid, $password)) {
            // PARAMS to view
            $params = [
                'projectid'=>$projectid,
                'password'=>$password,
                'cospend_version'=>$this->appVersion
            ];
            $response = new TemplateResponse('cospend', 'main', $params);
            $csp = new ContentSecurityPolicy();
            $csp->addAllowedImageDomain('*')
                ->addAllowedMediaDomain('*')
                //->addAllowedChildSrcDomain('*')
                ->addAllowedFrameDomain('*')
                ->addAllowedWorkerSrcDomain('*')
                ->addAllowedObjectDomain('*')
                ->addAllowedScriptDomain('*')
                ->addAllowedConnectDomain('*');
            $response->setContentSecurityPolicy($csp);
            return $response;
        }
        else {
            //$response = new DataResponse(null, 403);
            //return $response;
            $params = [
                'wrong'=>true,
                'cospend_version'=>$this->appVersion
            ];
            $response = new TemplateResponse('cospend', 'login', $params);
            $csp = new ContentSecurityPolicy();
            $csp->addAllowedImageDomain('*')
                ->addAllowedMediaDomain('*')
                //->addAllowedChildSrcDomain('*')
                ->addAllowedFrameDomain('*')
                ->addAllowedWorkerSrcDomain('*')
                ->addAllowedObjectDomain('*')
                ->addAllowedScriptDomain('*')
                ->addAllowedConnectDomain('*');
            $response->setContentSecurityPolicy($csp);
            return $response;
        }
    }

    private function checkLogin($projectId, $password) {
        if ($projectId === '' || $projectId === null ||
            $password === '' || $password === null
        ) {
            return false;
        }
        else {
            $qb = $this->dbconnection->getQueryBuilder();
            $qb->select('id', 'password')
               ->from('cospend_projects', 'p')
               ->where(
                   $qb->expr()->eq('id', $qb->createNamedParameter($projectId, IQueryBuilder::PARAM_STR))
               );
            $req = $qb->execute();
            $dbid = null;
            $dbPassword = null;
            while ($row = $req->fetch()){
                $dbid = $row['id'];
                $dbPassword = $row['password'];
                break;
            }
            $req->closeCursor();
            $qb = $qb->resetQueryParts();
            return (
                $password !== null &&
                $password !== '' &&
                $dbPassword !== null &&
                password_verify($password, $dbPassword)
            );
        }
    }

    /**
     * @NoAdminRequired
     *
     */
    public function webCreateProject($id, $name, $password) {
        $user = $this->userManager->get($this->userId);
        $userEmail = $user->getEMailAddress();
        $result = $this->projectService->createProject($name, $id, $password, $userEmail, $this->userId);
        if (is_string($result) and !is_array($result)) {
            // project id
            return new DataResponse($result);
        }
        else {
            return new DataResponse($result, 400);
        }
    }

    /**
     * @NoAdminRequired
     *
     */
    public function webAddExternalProject($id, $url, $password) {
        $result = $this->projectService->addExternalProject($url, $id, $password, $this->userId);
        if (!is_array($result) and is_string($result)) {
            // project id
            return new DataResponse($result);
        }
        else {
            return new DataResponse($result, 400);
        }
    }

    /**
     * @NoAdminRequired
     *
     */
    public function webDeleteProject($projectid) {
        if ($this->projectService->userCanAccessProject($this->userId, $projectid)) {
            $result = $this->projectService->deleteProject($projectid);
            if ($result === 'DELETED') {
                return new DataResponse($result);
            }
            else {
                return new DataResponse($result, 404);
            }
        }
        else {
            $response = new DataResponse(
                ['message'=>'You are not allowed to delete this project']
                , 403
            );
            return $response;
        }
    }

    /**
     * @NoAdminRequired
     *
     */
    public function webDeleteBill($projectid, $billid) {
        if ($this->projectService->userCanAccessProject($this->userId, $projectid)) {
            $result = $this->projectService->deleteBill($projectid, $billid);
            if ($result === 'OK') {
                return new DataResponse($result);
            }
            else {
                return new DataResponse($result, 404);
            }
        }
        else {
            $response = new DataResponse(
                ['message'=>'You are not allowed to delete this bill']
                , 403
            );
            return $response;
        }
    }

    /**
     * @NoAdminRequired
     *
     */
    public function webGetProjectInfo($projectid) {
        if ($this->projectService->userCanAccessProject($this->userId, $projectid)) {
            $projectInfo = $this->projectService->getProjectInfo($projectid);
            $response = new DataResponse($projectInfo);
            return $response;
        }
        else {
            $response = new DataResponse(
                ['message'=>'You are not allowed to get this project\'s info']
                , 403
            );
            return $response;
        }
    }

    /**
     * @NoAdminRequired
     *
     */
    public function webGetProjectStatistics($projectid, $dateMin=null, $dateMax=null, $paymentMode=null, $category=null,
                                            $amountMin=null, $amountMax=null) {
        if ($this->projectService->userCanAccessProject($this->userId, $projectid)) {
            $result = $this->projectService->getProjectStatistics($projectid, 'lowername', $dateMin, $dateMax, $paymentMode, $category, $amountMin, $amountMax);
            return new DataResponse($result);
        }
        else {
            $response = new DataResponse(
                ['message'=>'You are not allowed to get this project\'s statistics']
                , 403
            );
            return $response;
        }
    }

    /**
     * @NoAdminRequired
     *
     */
    public function webGetProjectSettlement($projectid) {
        if ($this->projectService->userCanAccessProject($this->userId, $projectid)) {
            $result = $this->projectService->getProjectSettlement($projectid);
            return new DataResponse($result);
        }
        else {
            $response = new DataResponse(
                ['message'=>'You are not allowed to get this project\'s settlement']
                , 403
            );
            return $response;
        }
    }

    /**
     * @NoAdminRequired
     *
     */
    public function webAutoSettlement($projectid) {
        if ($this->projectService->userCanAccessProject($this->userId, $projectid)) {
            $result = $this->projectService->autoSettlement($projectid);
            if ($result === 'OK') {
                return new DataResponse($result);
            }
            else {
                return new DataResponse($result, 403);
            }
        }
        else {
            $response = new DataResponse(
                ['message'=>'You are not allowed to settle this project automatically']
                , 403
            );
            return $response;
        }
    }

    /**
     * @NoAdminRequired
     *
     */
    public function webEditMember($projectid, $memberid, $name, $weight, $activated) {
        if ($this->projectService->userCanAccessProject($this->userId, $projectid)) {
            $result = $this->projectService->editMember($projectid, $memberid, $name, $weight, $activated);
            if (is_array($result) and array_key_exists('activated', $result)) {
                return new DataResponse($result);
            }
            else {
                return new DataResponse($result, 400);
            }
        }
        else {
            $response = new DataResponse(
                ['message'=>'You are not allowed to edit this member']
                , 403
            );
            return $response;
        }
    }

    /**
     * @NoAdminRequired
     *
     */
    public function webEditBill($projectid, $billid, $date, $what, $payer, $payed_for,
                                $amount, $repeat, $paymentmode=null, $categoryid=null) {
        if ($this->projectService->userCanAccessProject($this->userId, $projectid)) {
            $result =  $this->projectService->editBill(
                $projectid, $billid, $date, $what, $payer, $payed_for,
                $amount, $repeat, $paymentmode, $categoryid
            );
            if (is_numeric($result)) {
                // edited bill id
                return new DataResponse($result);
            }
            else {
                return new DataResponse($result, 400);
            }
        }
        else {
            $response = new DataResponse(
                ['message'=>'You are not allowed to edit this bill']
                , 403
            );
            return $response;
        }
    }

    /**
     * @NoAdminRequired
     *
     */
    public function webEditProject($projectid, $name, $contact_email, $password, $autoexport=null) {
        if ($this->projectService->userCanAccessProject($this->userId, $projectid)) {
            $result = $this->projectService->editProject($projectid, $name, $contact_email, $password, $autoexport);
            if ($result === 'UPDATED') {
                return new DataResponse($result);
            }
            else {
                return new DataResponse($result, 400);
            }
        }
        else {
            $response = new DataResponse(
                ['message'=>'You are not allowed to edit this project']
                , 403
            );
            return $response;
        }
    }

    /**
     * @NoAdminRequired
     *
     */
    public function webEditExternalProject($projectid, $ncurl, $password) {
        if ($this->projectService->userCanAccessExternalProject($this->userId, $projectid, $ncurl)) {
            $result = $this->projectService->editExternalProject($projectid, $ncurl, $password);
            if ($result === 'UPDATED') {
                return new DataResponse($result);
            }
            else {
                return new DataResponse($result, 403);
            }
        }
        else {
            $response = new DataResponse(
                ['message'=>'You are not allowed to edit this external project']
                , 400
            );
            return $response;
        }
    }

    /**
     * @NoAdminRequired
     *
     */
    public function webDeleteExternalProject($projectid, $ncurl) {
        if ($this->projectService->userCanAccessExternalProject($this->userId, $projectid, $ncurl)) {
            $result = $this->projectService->deleteExternalProject($projectid, $ncurl);
            if ($result === 'DELETED') {
                return new DataResponse($result);
            }
            else {
                return new DataResponse($result, 403);
            }
        }
        else {
            $response = new DataResponse(
                ['message'=>'You are not allowed to delete this external project']
                , 400
            );
            return $response;
        }
    }

    /**
     * @NoAdminRequired
     *
     */
    public function webAddBill($projectid, $date, $what, $payer, $payed_for, $amount,
                               $repeat, $paymentmode=null, $categoryid=null) {
        if ($this->projectService->userCanAccessProject($this->userId, $projectid)) {
            $result = $this->projectService->addBill(
                $projectid, $date, $what, $payer, $payed_for, $amount,
                $repeat, $paymentmode, $categoryid
            );
            if (is_numeric($result)) {
                // inserted bill id
                return new DataResponse($result);
            }
            else {
                return new DataResponse($result, 400);
            }
        }
        else {
            $response = new DataResponse(
                ['message'=>'You are not allowed to add a bill to this project']
                , 403
            );
            return $response;
        }
    }

    /**
     * @NoAdminRequired
     *
     */
    public function webAddMember($projectid, $name) {
        if ($this->projectService->userCanAccessProject($this->userId, $projectid)) {
            $result = $this->projectService->addMember($projectid, $name, 1);
            if (is_numeric($result)) {
                // inserted bill id
                return new DataResponse($result);
            }
            else {
                return new DataResponse($result, 400);
            }
        }
        else {
            $response = new DataResponse(
                ['message'=>'You are not allowed to add member to this project']
                , 403
            );
            return $response;
        }
    }

    /**
     * @NoAdminRequired
     *
     */
    public function webGetBills($projectid, $lastchanged=null) {
        if ($this->projectService->userCanAccessProject($this->userId, $projectid)) {
            $bills = $this->projectService->getBills($projectid, null, null, null, null, null, null, $lastchanged);
            $response = new DataResponse($bills);
            return $response;
        }
        else {
            $response = new DataResponse(
                ['message'=>'You are not allowed to get bills of this project']
                , 403
            );
            return $response;
        }
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     *
     */
    public function webGetProjects() {
        $response = new DataResponse(
            $this->projectService->getProjects($this->userId)
        );
        return $response;
    }

    /**
     * curl -X POST https://ihatemoney.org/api/projects \
     *   -d 'name=yay&id=yay&password=yay&contact_email=yay@notmyidea.org'
     *   "yay"
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     * @CORS
     */
    public function apiCreateProject($name, $id, $password, $contact_email) {
        $allow = intval($this->config->getAppValue('cospend', 'allowAnonymousCreation'));
        if ($allow) {
            $result = $this->projectService->createProject($name, $id, $password, $contact_email);
            if (is_string($result) and !is_array($result)) {
                // project id
                return new DataResponse($result);
            }
            else {
                return new DataResponse($result, 400);
            }
        }
        else {
            $response = new DataResponse(
                ['message'=>'Anonymous project creation is not allowed on this server']
                , 403
            );
            return $response;
        }
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     * @CORS
     */
    public function apiGetProjectInfo($projectid, $password) {
        if ($this->checkLogin($projectid, $password)) {
            $projectInfo = $this->projectService->getProjectInfo($projectid);
            if ($projectInfo !== null) {
                unset($projectInfo['userid']);
                return new DataResponse($projectInfo);
            }
            else {
                $response = new DataResponse(
                    ['message'=>'Project not found in the database']
                    , 404
                );
                return $response;
            }
        }
        else {
            $response = new DataResponse(
                ['message'=>'The server could not verify that you are authorized to access the URL requested.  You either supplied the wrong credentials (e.g. a bad password), or your browser doesn\'t understand how to supply the credentials required.']
                , 400
            );
            return $response;
        }
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     * @CORS
     */
    public function apiSetProjectInfo($projectid, $passwd, $name, $contact_email, $password, $autoexport=null) {
        if ($this->checkLogin($projectid, $passwd)) {
            $result = $this->projectService->editProject($projectid, $name, $contact_email, $password, $autoexport);
            if ($result === 'UPDATED') {
                return new DataResponse($result);
            }
            else {
                return new DataResponse($result, 400);
            }
        }
        else {
            $response = new DataResponse(
                ['message'=>'The server could not verify that you are authorized to access the URL requested.  You either supplied the wrong credentials (e.g. a bad password), or your browser doesn\'t understand how to supply the credentials required.']
                , 401
            );
            return $response;
        }
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     * @CORS
     */
    public function apiGetMembers($projectid, $password, $lastchanged=null) {
        if ($this->checkLogin($projectid, $password)) {
            $members = $this->projectService->getMembers($projectid, null, $lastchanged);
            $response = new DataResponse($members);
            return $response;
        }
        else {
            $response = new DataResponse(
                ['message'=>'The server could not verify that you are authorized to access the URL requested.  You either supplied the wrong credentials (e.g. a bad password), or your browser doesn\'t understand how to supply the credentials required.']
                , 401
            );
            return $response;
        }
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     * @CORS
     */
    public function apiGetBills($projectid, $password, $lastchanged=null) {
        if ($this->checkLogin($projectid, $password)) {
            $bills = $this->projectService->getBills($projectid, null, null, null, null, null, null, $lastchanged);
            $response = new DataResponse($bills);
            return $response;
        }
        else {
            $response = new DataResponse(
                ['message'=>'The server could not verify that you are authorized to access the URL requested.  You either supplied the wrong credentials (e.g. a bad password), or your browser doesn\'t understand how to supply the credentials required.']
                , 401
            );
            return $response;
        }
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     * @CORS
     */
    public function apiv2GetBills($projectid, $password, $lastchanged=null) {
        if ($this->checkLogin($projectid, $password)) {
            $bills = $this->projectService->getBills($projectid, null, null, null, null, null, null, $lastchanged);
            $billIds = $this->projectService->getAllBillIds($projectid);
            $ts = (new \DateTime())->getTimestamp();
            $response = new DataResponse([
                'bills'=>$bills,
                'allBillIds'=>$billIds,
                'timestamp'=>$ts
            ]);
            return $response;
        }
        else {
            $response = new DataResponse(
                ['message'=>'The server could not verify that you are authorized to access the URL requested.  You either supplied the wrong credentials (e.g. a bad password), or your browser doesn\'t understand how to supply the credentials required.']
                , 401
            );
            return $response;
        }
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     * @CORS
     */
    public function apiAddMember($projectid, $password, $name, $weight) {
        if ($this->checkLogin($projectid, $password)) {
            $result = $this->projectService->addMember($projectid, $name, $weight);
            if (is_numeric($result)) {
                // inserted bill id
                return new DataResponse($result);
            }
            else {
                return new DataResponse($result, 400);
            }
        }
        else {
            $response = new DataResponse(
                ['message'=>'The server could not verify that you are authorized to access the URL requested.  You either supplied the wrong credentials (e.g. a bad password), or your browser doesn\'t understand how to supply the credentials required.']
                , 401
            );
            return $response;
        }
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     * @CORS
     */
    public function apiAddBill($projectid, $password, $date, $what, $payer, $payed_for, $amount, $repeat='n', $paymentmode=null, $categoryid=null) {
        if ($this->checkLogin($projectid, $password)) {
            $result = $this->projectService->addBill($projectid, $date, $what, $payer, $payed_for, $amount, $repeat, $paymentmode, $categoryid);
            if (is_numeric($result)) {
                // inserted bill id
                return new DataResponse($result);
            }
            else {
                return new DataResponse($result, 400);
            }
        }
        else {
            $response = new DataResponse(
                ['message'=>'The server could not verify that you are authorized to access the URL requested.  You either supplied the wrong credentials (e.g. a bad password), or your browser doesn\'t understand how to supply the credentials required.']
                , 401
            );
            return $response;
        }
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     * @CORS
     */
    public function apiEditBill($projectid, $password, $billid, $date, $what, $payer, $payed_for,
                                $amount, $repeat='n', $paymentmode=null, $categoryid=null) {
        if ($this->checkLogin($projectid, $password)) {
            $result = $this->projectService->editBill($projectid, $billid, $date, $what, $payer, $payed_for,
                                                      $amount, $repeat, $paymentmode, $categoryid);
            if (is_numeric($result)) {
                // edited bill id
                return new DataResponse($result);
            }
            else {
                return new DataResponse($result, 400);
            }
        }
        else {
            $response = new DataResponse(
                ['message'=>'The server could not verify that you are authorized to access the URL requested.  You either supplied the wrong credentials (e.g. a bad password), or your browser doesn\'t understand how to supply the credentials required.']
                , 401
            );
            return $response;
        }
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     * @CORS
     */
    public function apiDeleteBill($projectid, $password, $billid) {
        if ($this->checkLogin($projectid, $password)) {
            $result = $this->projectService->deleteBill($projectid, $billid);
            if ($result === 'OK') {
                return new DataResponse($result);
            }
            else {
                return new DataResponse($result, 404);
            }
        }
        else {
            $response = new DataResponse(
                ['message'=>'The server could not verify that you are authorized to access the URL requested.  You either supplied the wrong credentials (e.g. a bad password), or your browser doesn\'t understand how to supply the credentials required.']
                , 401
            );
            return $response;
        }
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     * @CORS
     */
    public function apiDeleteMember($projectid, $password, $memberid) {
        if ($this->checkLogin($projectid, $password)) {
            $result = $this->projectService->deleteMember($projectid, $memberid);
            if ($result === 'OK') {
                return new DataResponse($result);
            }
            else {
                return new DataResponse($result, 404);
            }
        }
        else {
            $response = new DataResponse(
                ['message'=>'The server could not verify that you are authorized to access the URL requested.  You either supplied the wrong credentials (e.g. a bad password), or your browser doesn\'t understand how to supply the credentials required.']
                , 401
            );
            return $response;
        }
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     * @CORS
     */
    public function apiDeleteProject($projectid, $password) {
        if ($this->checkLogin($projectid, $password)) {
            $result = $this->projectService->deleteProject($projectid);
            if ($result === 'DELETED') {
                return new DataResponse($result);
            }
            else {
                return new DataResponse($result, 404);
            }
        }
        else {
            $response = new DataResponse(
                ['message'=>'The server could not verify that you are authorized to access the URL requested.  You either supplied the wrong credentials (e.g. a bad password), or your browser doesn\'t understand how to supply the credentials required.']
                , 401
            );
            return $response;
        }
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     * @CORS
     */
    public function apiEditMember($projectid, $password, $memberid, $name, $weight, $activated) {
        if ($this->checkLogin($projectid, $password)) {
            $result = $this->projectService->editMember($projectid, $memberid, $name, $weight, $activated);
            if (is_array($result) and array_key_exists('activated', $result)) {
                return new DataResponse($result);
            }
            else {
                return new DataResponse($result, 403);
            }
        }
        else {
            $response = new DataResponse(
                ['message'=>'The server could not verify that you are authorized to access the URL requested.  You either supplied the wrong credentials (e.g. a bad password), or your browser doesn\'t understand how to supply the credentials required.']
                , 401
            );
            return $response;
        }
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     * @CORS
     */
    public function apiGetProjectStatistics($projectid, $password, $dateMin=null, $dateMax=null, $paymentMode=null,
                                            $category=null, $amountMin=null, $amountMax=null) {
        if ($this->checkLogin($projectid, $password)) {
            $result = $this->projectService->getProjectStatistics($projectid, 'lowername', $dateMin, $dateMax, $paymentMode,
                                               $category, $amountMin, $amountMax);
            $response = new DataResponse($result);
        }
        else {
            $response = new DataResponse(
                ['message'=>'The server could not verify that you are authorized to access the URL requested.  You either supplied the wrong credentials (e.g. a bad password), or your browser doesn\'t understand how to supply the credentials required.']
                , 401
            );
            return $response;
        }
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     * @CORS
     */
    public function apiGetProjectSettlement($projectid, $password) {
        if ($this->checkLogin($projectid, $password)) {
            $result = $this->projectService->getProjectSettlement($projectid);
            $response = new DataResponse($result);
        }
        else {
            $response = new DataResponse(
                ['message'=>'The server could not verify that you are authorized to access the URL requested.  You either supplied the wrong credentials (e.g. a bad password), or your browser doesn\'t understand how to supply the credentials required.']
                , 401
            );
            return $response;
        }
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     * @CORS
     */
    public function apiAutoSettlement($projectid, $password) {
        if ($this->checkLogin($projectid, $password)) {
            $result = $this->projectService->autoSettlement($projectid);
            if ($result === 'OK') {
                return new DataResponse($result);
            }
            else {
                return new DataResponse($result, 403);
            }
        }
        else {
            $response = new DataResponse(
                ['message'=>'The server could not verify that you are authorized to access the URL requested.  You either supplied the wrong credentials (e.g. a bad password), or your browser doesn\'t understand how to supply the credentials required.']
                , 401
            );
            return $response;
        }
    }

    /**
     * @NoAdminRequired
     */
    public function getUserList() {
        $userNames = [];
        foreach($this->userManager->search('') as $u) {
            if ($u->getUID() !== $this->userId && $u->isEnabled()) {
                $userNames[$u->getUID()] = $u->getDisplayName();
            }
        }
        $groupNames = [];
        foreach($this->groupManager->search('') as $g) {
            $groupNames[$g->getGID()] = $g->getDisplayName();
        }
        $response = new DataResponse(
            [
                'users'=>$userNames,
                'groups'=>$groupNames
            ]
        );
        $csp = new ContentSecurityPolicy();
        $csp->addAllowedImageDomain('*')
            ->addAllowedMediaDomain('*')
            ->addAllowedConnectDomain('*');
        $response->setContentSecurityPolicy($csp);
        return $response;
    }

    /**
     * @NoAdminRequired
     */
    public function addUserShare($projectid, $userid) {
        if ($this->projectService->userCanAccessProject($this->userId, $projectid)) {
            $result = $this->projectService->addUserShare($projectid, $userid, $this->userId);
            if ($result === 'OK') {
                return new DataResponse($result);
            }
            else {
                return new DataResponse($result, 400);
            }
        }
        else {
            $response = new DataResponse(
                ['message'=>'You are not allowed to edit this project']
                , 403
            );
            return $response;
        }
    }

    /**
     * @NoAdminRequired
     */
    public function deleteUserShare($projectid, $userid) {
        if ($this->projectService->userCanAccessProject($this->userId, $projectid)) {
            $result = $this->projectService->deleteUserShare($projectid, $userid, $this->userId);
            if ($result === 'OK') {
                return new DataResponse($result);
            }
            else {
                return new DataResponse($result, 400);
            }
        }
        else {
            $response = new DataResponse(
                ['message'=>'You are not allowed to edit this project']
                , 403
            );
            return $response;
        }
    }

    /**
     * @NoAdminRequired
     */
    public function addGroupShare($projectid, $groupid) {
        if ($this->projectService->userCanAccessProject($this->userId, $projectid)) {
            $result = $this->projectService->addGroupShare($projectid, $groupid, $this->userId);
            if ($result === 'OK') {
                return new DataResponse($result);
            }
            else {
                return new DataResponse($result, 400);
            }
        }
        else {
            $response = new DataResponse(
                ['message'=>'You are not allowed to edit this project']
                , 403
            );
            return $response;
        }
    }

    /**
     * @NoAdminRequired
     */
    public function deleteGroupShare($projectid, $groupid) {
        if ($this->projectService->userCanAccessProject($this->userId, $projectid)) {
            $result = $this->projectService->deleteGroupShare($projectid, $groupid, $this->userId);
            if ($result === 'OK') {
                return new DataResponse($result);
            }
            else {
                return new DataResponse($result, 400);
            }
        }
        else {
            $response = new DataResponse(
                ['message'=>'You are not allowed to edit this project']
                , 403
            );
            return $response;
        }
    }

    /**
     * @NoAdminRequired
     */
    public function getPublicFileShare($path) {
        $cleanPath = str_replace(array('../', '..\\'), '',  $path);
        $userFolder = \OC::$server->getUserFolder();
        if ($userFolder->nodeExists($cleanPath)) {
            $file = $userFolder->get($cleanPath);
            if ($file->getType() === \OCP\Files\FileInfo::TYPE_FILE) {
                if ($file->isShareable()) {
                    $shares = $this->shareManager->getSharesBy($this->userId,
                        \OCP\Share::SHARE_TYPE_LINK, $file, false, 1, 0);
                    if (count($shares) > 0){
                        foreach($shares as $share){
                            if ($share->getPassword() === null){
                                $token = $share->getToken();
                                break;
                            }
                        }
                    }
                    else {
                        $share = $this->shareManager->newShare();
                        $share->setNode($file);
                        $share->setPermissions(Constants::PERMISSION_READ);
                        $share->setShareType(Share::SHARE_TYPE_LINK);
                        $share->setSharedBy($this->userId);
                        $share = $this->shareManager->createShare($share);
                        $token = $share->getToken();
                    }
                    $response = new DataResponse(['token'=>$token]);
                }
                else {
                    $response = new DataResponse(['message'=>'Access denied'], 403);
                }
            }
            else {
                $response = new DataResponse(['message'=>'Access denied'], 403);
            }
        }
        else {
            $response = new DataResponse(['message'=>'Access denied'], 403);
        }
        return $response;
    }

    /**
     * @NoAdminRequired
     */
    public function exportCsvSettlement($projectid) {
        if ($this->projectService->userCanAccessProject($this->userId, $projectid)) {
            $result = $this->projectService->exportCsvSettlement($projectid, $this->userId);
            if (is_array($result) and array_key_exists('path', $result)) {
                return new DataResponse($result);
            }
            else {
                return new DataResponse($result, 400);
            }
        }
        else {
            $response = new DataResponse(
                ['message'=>'You are not allowed to export this project settlement']
                , 403
            );
            return $response;
        }
    }

    /**
     * @NoAdminRequired
     */
    public function exportCsvStatistics($projectid, $dateMin=null, $dateMax=null, $paymentMode=null, $category=null,
                                        $amountMin=null, $amountMax=null) {
        if ($this->projectService->userCanAccessProject($this->userId, $projectid)) {
            $result = $this->projectService->exportCsvStatistics($projectid, $this->userId, $dateMin, $dateMax,
                                                                 $paymentMode, $category, $amountMin, $amountMax);
            if (is_array($result) and array_key_exists('path', $result)) {
                return new DataResponse($result);
            }
            else {
                return new DataResponse($result, 400);
            }
        }
        else {
            $response = new DataResponse(
                ['message'=>'You are not allowed to export this project statistics']
                , 403
            );
            return $response;
        }
    }

    /**
     * @NoAdminRequired
     */
    public function exportCsvProject($projectid, $name=null, $uid=null) {
        $userId = $uid;
        if ($this->userId) {
            $userId = $this->userId;
        }

        if ($this->projectService->userCanAccessProject($userId, $projectid)) {
            $result = $this->projectService->exportCsvProject($projectid, $name, $userId);
            if (is_array($result) and array_key_exists('path', $result)) {
                return new DataResponse($result);
            }
            else {
                return new DataResponse($result, 400);
            }
        }
        else {
            $response = new DataResponse(
                ['message'=>'You are not allowed to export this project']
                , 403
            );
            return $response;
        }
    }

    /**
     * @NoAdminRequired
     */
    public function importCsvProject($path) {
        $result = $this->projectService->importCsvProject($path, $this->userId);
        if (!is_array($result) and is_string($result)) {
            return new DataResponse($result);
        }
        else {
            return new DataResponse($result, 400);
        }
    }

    /**
     * Used by MoneyBuster to check if weblogin is valid
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function apiPing() {
        $response = new DataResponse(
            [$this->userId]
        );
        $csp = new ContentSecurityPolicy();
        $csp->addAllowedImageDomain('*')
            ->addAllowedMediaDomain('*')
            ->addAllowedConnectDomain('*');
        $response->setContentSecurityPolicy($csp);
        return $response;
    }

}
