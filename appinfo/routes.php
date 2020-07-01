<?php
/**
 * Nextcloud - cospend
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Julien Veyssier <eneiluj@posteo.net>
 * @copyright Julien Veyssier 2018
 */

return [
    'routes' => [
        ['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],

        // api for client using guest access (password)
        [
            'name'         => 'page#preflighted_cors',
            'url'          => '/api/{path}',
            'verb'         => 'OPTIONS',
            'requirements' => ['path' => '.+']
        ],
        [
            'name'         => 'page#preflighted_cors',
            'url'          => '/apiv2/{path}',
            'verb'         => 'OPTIONS',
            'requirements' => ['path' => '.+']
        ],
        ['name' => 'page#apiPing', 'url' => '/api/ping', 'verb' => 'GET'],
        ['name' => 'page#apiCreateProject', 'url' => '/api/projects', 'verb' => 'POST'],
        ['name' => 'page#apiGetProjectInfo', 'url' => '/api/projects/{projectid}/{password}', 'verb' => 'GET'],
        ['name' => 'page#apiSetProjectInfo', 'url' => '/api/projects/{projectid}/{passwd}', 'verb' => 'PUT'],
        ['name' => 'page#apiDeleteProject', 'url' => '/api/projects/{projectid}/{password}', 'verb' => 'DELETE'],
        ['name' => 'page#apiGetMembers', 'url' => '/api/projects/{projectid}/{password}/members', 'verb' => 'GET'],
        ['name' => 'page#apiAddMember', 'url' => '/api/projects/{projectid}/{password}/members', 'verb' => 'POST'],
        ['name' => 'page#apiv2AddMember', 'url' => '/apiv2/projects/{projectid}/{password}/members', 'verb' => 'POST'],
        ['name' => 'page#apiEditMember', 'url' => '/api/projects/{projectid}/{password}/members/{memberid}', 'verb' => 'PUT'],
        ['name' => 'page#apiDeleteMember', 'url' => '/api/projects/{projectid}/{password}/members/{memberid}', 'verb' => 'DELETE'],
        ['name' => 'page#apiGetBills', 'url' => '/api/projects/{projectid}/{password}/bills', 'verb' => 'GET'],
        ['name' => 'page#apiAddBill', 'url' => '/api/projects/{projectid}/{password}/bills', 'verb' => 'POST'],
        ['name' => 'page#apiEditBill', 'url' => '/api/projects/{projectid}/{password}/bills/{billid}', 'verb' => 'PUT'],
        ['name' => 'page#apiDeleteBill', 'url' => '/api/projects/{projectid}/{password}/bills/{billid}', 'verb' => 'DELETE'],
        ['name' => 'page#apiv2GetBills', 'url' => '/apiv2/projects/{projectid}/{password}/bills', 'verb' => 'GET'],
        ['name' => 'page#apiGetProjectStatistics', 'url' => '/api/projects/{projectid}/{password}/statistics', 'verb' => 'GET'],
        ['name' => 'page#apiGetProjectSettlement', 'url' => '/api/projects/{projectid}/{password}/settle', 'verb' => 'GET'],
        ['name' => 'page#apiAutoSettlement', 'url' => '/api/projects/{projectid}/{password}/autosettlement', 'verb' => 'GET'],
        ['name' => 'page#apiAddCurrency', 'url' => '/api/projects/{projectid}/{password}/currency', 'verb' => 'POST'],
        ['name' => 'page#apiEditCurrency', 'url' => '/api/projects/{projectid}/{password}/currency/{currencyid}', 'verb' => 'PUT'],
        ['name' => 'page#apiDeleteCurrency', 'url' => '/api/projects/{projectid}/{password}/currency/{currencyid}', 'verb' => 'DELETE'],
        ['name' => 'page#apiAddCategory', 'url' => '/api/projects/{projectid}/{password}/category', 'verb' => 'POST'],
        ['name' => 'page#apiEditCategory', 'url' => '/api/projects/{projectid}/{password}/category/{categoryid}', 'verb' => 'PUT'],
        ['name' => 'page#apiDeleteCategory', 'url' => '/api/projects/{projectid}/{password}/category/{categoryid}', 'verb' => 'DELETE'],
        ['name' => 'page#apiEditGuestAccessLevel', 'url' => '/api/projects/{projectid}/{password}/guest-access-level', 'verb' => 'PUT'],

        // api for logged in clients
        [
            'name'         => 'page#preflighted_cors',
            'url'          => '/api-priv/{path}',
            'verb'         => 'OPTIONS',
            'requirements' => ['path' => '.+']
        ],
        ['name' => 'page#apiPrivCreateProject', 'url' => '/api-priv/projects', 'verb' => 'POST'],
        ['name' => 'page#apiPrivGetProjectInfo', 'url' => '/api-priv/projects/{projectid}', 'verb' => 'GET'],
        ['name' => 'page#apiPrivSetProjectInfo', 'url' => '/api-priv/projects/{projectid}', 'verb' => 'PUT'],
        ['name' => 'page#apiPrivDeleteProject', 'url' => '/api-priv/projects/{projectid}', 'verb' => 'DELETE'],
        ['name' => 'page#apiPrivGetMembers', 'url' => '/api-priv/projects/{projectid}/members', 'verb' => 'GET'],
        ['name' => 'page#apiPrivAddMember', 'url' => '/api-priv/projects/{projectid}/members', 'verb' => 'POST'],
        ['name' => 'page#apiPrivEditMember', 'url' => '/api-priv/projects/{projectid}/members/{memberid}', 'verb' => 'PUT'],
        ['name' => 'page#apiPrivDeleteMember', 'url' => '/api-priv/projects/{projectid}/members/{memberid}', 'verb' => 'DELETE'],
        ['name' => 'page#apiPrivGetBills', 'url' => '/api-priv/projects/{projectid}/bills', 'verb' => 'GET'],
        ['name' => 'page#apiPrivAddBill', 'url' => '/api-priv/projects/{projectid}/bills', 'verb' => 'POST'],
        ['name' => 'page#apiPrivEditBill', 'url' => '/api-priv/projects/{projectid}/bills/{billid}', 'verb' => 'PUT'],
        ['name' => 'page#apiPrivDeleteBill', 'url' => '/api-priv/projects/{projectid}/bills/{billid}', 'verb' => 'DELETE'],
        ['name' => 'page#apiPrivGetProjectStatistics', 'url' => '/api-priv/projects/{projectid}/statistics', 'verb' => 'GET'],
        ['name' => 'page#apiPrivGetProjectSettlement', 'url' => '/api-priv/projects/{projectid}/settle', 'verb' => 'GET'],
        ['name' => 'page#apiPrivAutoSettlement', 'url' => '/api-priv/projects/{projectid}/autosettlement', 'verb' => 'GET'],
        ['name' => 'page#apiPrivAddCurrency', 'url' => '/api-priv/projects/{projectid}/currency', 'verb' => 'POST'],
        ['name' => 'page#apiPrivEditCurrency', 'url' => '/api-priv/projects/{projectid}/currency/{currencyid}', 'verb' => 'PUT'],
        ['name' => 'page#apiPrivDeleteCurrency', 'url' => '/api-priv/projects/{projectid}/currency/{currencyid}', 'verb' => 'DELETE'],
        ['name' => 'page#apiPrivAddCategory', 'url' => '/api-priv/projects/{projectid}/category', 'verb' => 'POST'],
        ['name' => 'page#apiPrivEditCategory', 'url' => '/api-priv/projects/{projectid}/category/{categoryid}', 'verb' => 'PUT'],
        ['name' => 'page#apiPrivDeleteCategory', 'url' => '/api-priv/projects/{projectid}/category/{categoryid}', 'verb' => 'DELETE'],

        ['name' => 'utils#getOptionsValues', 'url' => '/option-values', 'verb' => 'GET'],
        ['name' => 'utils#saveOptionValue', 'url' => '/option-value', 'verb' => 'PUT'],
        ['name' => 'utils#setAllowAnonymousCreation', 'url' => '/allow-anonymous-creation', 'verb' => 'PUT'],
        ['name' => 'page#getUserList', 'url' => '/user-list', 'verb' => 'GET'],
        ['name' => 'page#getMemberSuggestions', 'url' => '/projects/{projectid}/member-suggestions', 'verb' => 'GET'],
        ['name' => 'page#addCurrency', 'url' => '/projects/{projectid}/currency', 'verb' => 'POST'],
        ['name' => 'page#editCurrency', 'url' => '/projects/{projectid}/currency/{currencyid}', 'verb' => 'PUT'],
        ['name' => 'page#deleteCurrency', 'url' => '/projects/{projectid}/currency/{currencyid}', 'verb' => 'DELETE'],
        ['name' => 'page#addCategory', 'url' => '/projects/{projectid}/category', 'verb' => 'POST'],
        ['name' => 'page#editCategory', 'url' => '/projects/{projectid}/category/{categoryid}', 'verb' => 'PUT'],
        ['name' => 'page#deleteCategory', 'url' => '/projects/{projectid}/category/{categoryid}', 'verb' => 'DELETE'],
        ['name' => 'page#addUserShare', 'url' => '/projects/{projectid}/user-share', 'verb' => 'POST'],
        ['name' => 'page#deleteUserShare', 'url' => '/projects/{projectid}/user-share/{shid}', 'verb' => 'DELETE'],
        ['name' => 'page#addGroupShare', 'url' => '/projects/{projectid}/group-share', 'verb' => 'POST'],
        ['name' => 'page#deleteGroupShare', 'url' => '/projects/{projectid}/group-share/{shid}', 'verb' => 'DELETE'],
        ['name' => 'page#addCircleShare', 'url' => '/projects/{projectid}/circle-share', 'verb' => 'POST'],
        ['name' => 'page#deleteCircleShare', 'url' => '/projects/{projectid}/circle-share/{shid}', 'verb' => 'DELETE'],
        ['name' => 'page#addPublicShare', 'url' => '/projects/{projectid}/public-share', 'verb' => 'POST'],
        ['name' => 'page#deletePublicShare', 'url' => '/projects/{projectid}/public-share/{shid}', 'verb' => 'DELETE'],
        ['name' => 'page#editShareAccessLevel', 'url' => '/projects/{projectid}/share-access-level/{shid}', 'verb' => 'PUT'],
        ['name' => 'page#editGuestAccessLevel', 'url' => '/projects/{projectid}/guest-access-level', 'verb' => 'PUT'],
        ['name' => 'page#getPublicFileShare', 'url' => '/getPublicFileShare', 'verb' => 'POST'],
        ['name' => 'page#importCsvProject', 'url' => '/import-csv-project', 'verb' => 'GET'],
        ['name' => 'page#importSWProject', 'url' => '/import-sw-project', 'verb' => 'GET'],
        ['name' => 'page#exportCsvProject', 'url' => '/export-csv-project/{projectid}', 'verb' => 'GET'],
        ['name' => 'page#exportCsvStatistics', 'url' => '/export-csv-statistics/{projectid}', 'verb' => 'GET'],
        ['name' => 'page#exportCsvSettlement', 'url' => '/export-csv-settlement/{projectid}', 'verb' => 'GET'],
        ['name' => 'page#webGetProjects', 'url' => '/projects', 'verb' => 'GET'],
        ['name' => 'page#webGetProjects', 'url' => '/getProjects', 'verb' => 'POST'],
        ['name' => 'page#webCreateProject', 'url' => '/projects', 'verb' => 'POST'],
        ['name' => 'page#webEditProject', 'url' => '/projects/{projectid}', 'verb' => 'PUT'],
        ['name' => 'page#webDeleteProject', 'url' => '/projects/{projectid}', 'verb' => 'DELETE'],
        ['name' => 'page#webAddMember', 'url' => '/projects/{projectid}/members', 'verb' => 'POST'],
        ['name' => 'page#webEditMember', 'url' => '/projects/{projectid}/members/{memberid}', 'verb' => 'PUT'],
        ['name' => 'page#webGetBills', 'url' => '/projects/{projectid}/bills', 'verb' => 'GET'],
        ['name' => 'page#webAddBill', 'url' => '/projects/{projectid}/bills', 'verb' => 'POST'],
        ['name' => 'page#webEditBill', 'url' => '/projects/{projectid}/bills/{billid}', 'verb' => 'PUT'],
        ['name' => 'page#webDeleteBill', 'url' => '/projects/{projectid}/bills/{billid}', 'verb' => 'DELETE'],
        ['name' => 'page#webGetProjectInfo', 'url' => '/projects/{projectid}', 'verb' => 'GET'],
        ['name' => 'page#webGetProjectStatistics', 'url' => '/projects/{projectid}/statistics', 'verb' => 'GET'],
        ['name' => 'page#webGetProjectSettlement', 'url' => '/projects/{projectid}/settlement', 'verb' => 'GET'],
        ['name' => 'page#webAutoSettlement', 'url' => '/projects/{projectid}/auto-settlement', 'verb' => 'GET'],
        ['name' => 'page#webCheckPassword', 'url' => 'checkpassword/{projectid}/{password}', 'verb' => 'GET'],
        ['name' => 'page#pubLoginProjectPassword', 'url' => 'loginproject/{projectid}/{password}', 'verb' => 'GET'],
        ['name' => 'page#pubLoginProject', 'url' => 'loginproject/{projectid}', 'verb' => 'GET'],
        ['name' => 'page#pubLogin', 'url' => 'login', 'verb' => 'GET'],
        ['name' => 'page#pubProject', 'url' => 'project', 'verb' => 'POST'],
        ['name' => 'page#publicShareLinkPage', 'url' => 's/{token}', 'verb' => 'GET'],

        ['name' => 'utils#getAvatar', 'url' => 'getAvatar', 'verb' => 'GET'],
    ]
];
