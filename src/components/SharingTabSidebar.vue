<template>
	<div>
		<NcSelect
			v-if="editionAccess"
			v-model="selectedSharee"
			class="shareInput"
			:placeholder="t('cospend', 'Share project with a user, group or circle …')"
			:options="formatedSharees"
			:append-to-body="false"
			label="displayName"
			@search="asyncFind"
			@input="clickShareeItem">
			<template #option="option">
				<div class="shareSelectOption">
					<NcAvatar v-if="option.type === constants.SHARE_TYPE.USER"
						class="avatar-option"
						:user="option.user"
						:show-user-status="false" />
					<NcAvatar v-else-if="[constants.SHARE_TYPE.GROUP, constants.SHARE_TYPE.CIRCLE].includes(option.type)"
						class="avatar-option"
						:display-name="option.name"
						:is-no-user="true"
						:show-user-status="false" />
					<span class="multiselect-name">
						{{ option.displayName }}
					</span>
					<div v-if="option.type === constants.SHARE_TYPE.USER" class="multiselect-icon">
						<AccountIcon :size="20" />
					</div>
					<div v-else-if="option.type === constants.SHARE_TYPE.GROUP" class="multiselect-icon">
						<AccountGroupIcon :size="20" />
					</div>
					<div v-else-if="option.type === constants.SHARE_TYPE.CIRCLE" class="multiselect-icon">
						<GoogleCirclesCommunitiesIcon :size="20" />
					</div>
				</div>
			</template>
			<template #noOptions>
				{{ t('cospend', 'Start typing to search') }}
			</template>
		</NcSelect>

		<NcModal v-if="shareLinkQrcodeUrl"
			size="small"
			@close="closeQrcodeModal">
			<div class="qrcode-modal-content">
				<div class="qrcode-wrapper">
					<QRCode render="svg"
						:link="shareLinkQrcodeUrl"
						:fgcolor="qrcodeColor"
						:image-url="qrcodeImageUrl"
						:rounded="100" />
				</div>
				<hr>
				<p>
					{{ t('cospend', 'Scan this QRCode with your mobile device to add project "{name}" in MoneyBuster or PayForMe', { name: project.name }) }}
				</p>
				<hr>
				<p>
					{{ t('cospend', 'QRCode content: ') + shareLinkQrcodeUrl }}
				</p>
			</div>
		</NcModal>

		<ul
			id="shareWithList"
			ref="shareWithList"
			class="shareWithList">
			<li v-if="editionAccess && linkShares.length === 0"
				class="add-public-link-line"
				@click="addLink">
				<div :class="'avatardiv link-icon' + (addingPublicLink ? ' loading' : '')">
					<LinkVariantIcon :size="20" />
				</div>
				<span class="username">
					{{ t('cospend', 'Share link') }}
				</span>
				<NcActions>
					<NcActionButton>
						<template #icon>
							<PlusIcon :size="20" />
						</template>
						{{ t('cospend', 'Create a new share link') }}
					</NcActionButton>
				</NcActions>
			</li>
			<li v-for="access in linkShares" :key="access.id">
				<div class="avatardiv link-icon">
					<LinkVariantIcon :size="20" />
				</div>
				<span class="username">
					<span>{{ t('cospend', 'Share link') + (access.label ? ' (' + access.label + ')' : '') }}</span>
				</span>

				<NcActions>
					<NcActionLink
						:href="generatePublicLink(access)"
						target="_blank"
						@click.stop.prevent="copyLink(access)">
						{{ linkCopied[access.id] ? t('cospend', 'Link copied') : t('cospend', 'Copy to clipboard') }}
						<template #icon>
							<ClipboardCheckOutlineIcon v-if="linkCopied[access.id]"
								class="success"
								:size="20" />
							<ClippyIcon v-else
								:size="16" />
						</template>
					</NcActionLink>
				</NcActions>

				<NcActions>
					<NcActionLink
						:href="generateCospendLink(access)"
						target="_blank"
						@click.stop.prevent="displayCospendLinkQRCode(access)">
						<template #icon>
							<QrcodeIcon :size="20" />
						</template>
						{{ t('cospend', 'Show QRCode for mobile clients') }}
					</NcActionLink>
				</NcActions>

				<NcActions
					:force-menu="true"
					placement="bottom">
					<NcActionInput
						type="text"
						:value="access.label ?? ''"
						:disabled="!editionAccess || myAccessLevel < access.accesslevel"
						@submit="submitLabel(access, $event)">
						<template #icon>
							<TextBoxIcon :size="20" />
						</template>
						{{ t('cospend', 'Label') }}
					</NcActionInput>
					<NcActionCheckbox
						:checked="access.password !== null"
						:disabled="!editionAccess || myAccessLevel < access.accesslevel"
						@check="onPasswordCheck(access, $event)"
						@uncheck="onPasswordUncheck(access, $event)">
						{{ t('cospend', 'Password protect') }}
					</NcActionCheckbox>
					<NcActionInput
						v-if="access.password !== null"
						type="password"
						:value="access.password"
						:disabled="!editionAccess || myAccessLevel < access.accesslevel"
						@submit="submitPassword(access, $event)">
						<template #icon>
							<LockIcon :size="20" />
						</template>
						{{ t('cospend', 'Set link password') }}
					</NcActionInput>
					<NcActionSeparator />
					<NcActionRadio name="accessLevel"
						:disabled="!canSetAccessLevel(constants.ACCESS.VIEWER, access)"
						:checked="access.accesslevel === constants.ACCESS.VIEWER"
						@change="clickAccessLevel(access, constants.ACCESS.VIEWER)">
						{{ t('cospend', 'Viewer') }}
					</NcActionRadio>
					<NcActionRadio name="accessLevel"
						:disabled="!canSetAccessLevel(constants.ACCESS.PARTICIPANT, access)"
						:checked="access.accesslevel === constants.ACCESS.PARTICIPANT"
						@change="clickAccessLevel(access, constants.ACCESS.PARTICIPANT)">
						{{ t('cospend', 'Participant') }}
					</NcActionRadio>
					<NcActionRadio name="accessLevel"
						:disabled="!canSetAccessLevel(constants.ACCESS.MAINTENER, access)"
						:checked="access.accesslevel === constants.ACCESS.MAINTENER"
						@change="clickAccessLevel(access, constants.ACCESS.MAINTENER)">
						{{ t('cospend', 'Maintainer') }}
					</NcActionRadio>
					<NcActionRadio name="accessLevel"
						:disabled="!canSetAccessLevel(constants.ACCESS.ADMIN, access)"
						:checked="access.accesslevel === constants.ACCESS.ADMIN"
						@change="clickAccessLevel(access, constants.ACCESS.ADMIN)">
						{{ t('cospend', 'Admin') }}
					</NcActionRadio>
					<NcActionSeparator />
					<NcActionButton v-if="editionAccess && myAccessLevel > access.accesslevel"
						@click="clickDeleteAccess(access)">
						<template #icon>
							<DeleteIcon :size="20" />
						</template>
						{{ t('cospend', 'Delete link') }}
					</NcActionButton>
					<NcActionButton v-if="editionAccess"
						:close-after-click="true"
						@click="addLink">
						<template #icon>
							<PlusIcon :size="20" />
						</template>
						{{ t('cospend', 'Add another link') }}
					</NcActionButton>
				</NcActions>
			</li>
			<li>
				<NcAvatar :disable-menu="true" :disable-tooltip="true" :user="project.userid" />
				<span class="has-tooltip username">
					{{ project.userid }}
					<span class="project-owner-label">
						({{ t('cospend', 'Project owner') }})
					</span>
				</span>
			</li>
			<li v-for="access in ugcShares" :key="access.id">
				<NcAvatar
					v-if="access.type === constants.SHARE_TYPE.USER"
					:user="access.userid"
					:disable-menu="true"
					:disable-tooltip="true" />
				<div v-if="access.type === constants.SHARE_TYPE.GROUP"
					class="avatardiv link-icon">
					<AccountGroupIcon :size="20" />
				</div>
				<div v-if="access.type === constants.SHARE_TYPE.CIRCLE"
					class="avatardiv link-icon">
					<GoogleCirclesCommunitiesIcon :size="20" />
				</div>
				<span class="username">
					<span>{{ access.name }}</span>
				</span>

				<NcActions
					:force-menu="true"
					placement="bottom">
					<NcActionRadio name="accessLevel"
						:disabled="!canSetAccessLevel(constants.ACCESS.VIEWER, access)"
						:checked="access.accesslevel === constants.ACCESS.VIEWER"
						@change="clickAccessLevel(access, constants.ACCESS.VIEWER)">
						{{ t('cospend', 'Viewer') }}
					</NcActionRadio>
					<NcActionRadio name="accessLevel"
						:disabled="!canSetAccessLevel(constants.ACCESS.PARTICIPANT, access)"
						:checked="access.accesslevel === constants.ACCESS.PARTICIPANT"
						@change="clickAccessLevel(access, constants.ACCESS.PARTICIPANT)">
						{{ t('cospend', 'Participant') }}
					</NcActionRadio>
					<NcActionRadio name="accessLevel"
						:disabled="!canSetAccessLevel(constants.ACCESS.MAINTENER, access)"
						:checked="access.accesslevel === constants.ACCESS.MAINTENER"
						@change="clickAccessLevel(access, constants.ACCESS.MAINTENER)">
						{{ t('cospend', 'Maintainer') }}
					</NcActionRadio>
					<NcActionRadio name="accessLevel"
						:disabled="!canSetAccessLevel(constants.ACCESS.ADMIN, access)"
						:checked="access.accesslevel === constants.ACCESS.ADMIN"
						@change="clickAccessLevel(access, constants.ACCESS.ADMIN)">
						{{ t('cospend', 'Admin') }}
					</NcActionRadio>
					<NcActionButton v-if="editionAccess && myAccessLevel > access.accesslevel"
						@click="clickDeleteAccess(access)">
						<template #icon>
							<DeleteIcon :size="20" />
						</template>
						{{ t('cospend', 'Delete access') }}
					</NcActionButton>
				</NcActions>
			</li>
		</ul>
		<hr><br>
		<NcButton class="passwordAccessSwitch"
			@click="toggleShowPasswordAccess">
			<template #icon>
				<MenuDownIcon v-if="showPasswordAccess" :size="20" />
				<MenuRightIcon v-else :size="20" />
			</template>
			{{ t('cospend', 'Show deprecated password protected access') }}
		</NcButton>
		<div v-if="showPasswordAccess">
			<NcDialog :open.sync="showOldLinkInfo"
				:name="t('cospend', 'Info')"
				:message="t('cospend', 'Password protected access is now deprecated. You can use the share links instead, they can be password protected too.')" />
			<ul
				id="guestList"
				class="shareWithList">
				<li>
					<LockIcon :size="20" />
					<span class="username">
						<span>{{ t('cospend', 'Password protected access') }}</span>
					</span>
					<NcActions>
						<NcActionButton
							@click="showOldLinkInfo = true">
							<template #icon>
								<InformationVariantIcon :size="20" />
							</template>
							{{ t('cospend', 'More information') }}
						</NcActionButton>
					</NcActions>
					<NcActions>
						<NcActionLink
							:href="guestLink"
							target="_blank"
							@click.stop.prevent="copyPasswordLink">
							<template #icon>
								<ClipboardCheckOutlineIcon v-if="guestLinkCopied"
									class="success"
									:size="20" />
								<ClippyIcon v-else
									:size="16" />
							</template>
							{{ guestLinkCopied ? t('cospend', 'Link copied') : t('cospend', 'Copy to clipboard') }}
						</NcActionLink>
					</NcActions>
					<NcActions
						:force-menu="true"
						placement="bottom">
						<NcActionRadio name="guestAccessLevel"
							:disabled="myAccessLevel < constants.ACCESS.ADMIN"
							:checked="project.guestaccesslevel === constants.ACCESS.VIEWER"
							@change="clickGuestAccessLevel(constants.ACCESS.VIEWER)">
							{{ t('cospend', 'Viewer') }}
						</NcActionRadio>
						<NcActionRadio name="guestAccessLevel"
							:disabled="myAccessLevel < constants.ACCESS.ADMIN"
							:checked="project.guestaccesslevel === constants.ACCESS.PARTICIPANT"
							@change="clickGuestAccessLevel(constants.ACCESS.PARTICIPANT)">
							{{ t('cospend', 'Participant') }}
						</NcActionRadio>
						<NcActionRadio name="guestAccessLevel"
							:disabled="myAccessLevel < constants.ACCESS.ADMIN"
							:checked="project.guestaccesslevel === constants.ACCESS.MAINTENER"
							@change="clickGuestAccessLevel(constants.ACCESS.MAINTENER)">
							{{ t('cospend', 'Maintainer') }}
						</NcActionRadio>
						<NcActionRadio name="guestAccessLevel"
							:disabled="myAccessLevel < constants.ACCESS.ADMIN"
							:checked="project.guestaccesslevel === constants.ACCESS.ADMIN"
							@change="clickGuestAccessLevel(constants.ACCESS.ADMIN)">
							{{ t('cospend', 'Admin') }}
						</NcActionRadio>
					</NcActions>
				</li>
			</ul>
			<div v-if="myAccessLevel === constants.ACCESS.ADMIN"
				class="enterPassword">
				<input
					v-model="newGuestPassword"
					type="password"
					autocomplete="off"
					:placeholder="t('cospend', 'Set project password')"
					:readonly="newPasswordReadonly"
					@focus="newPasswordReadonly = false; $event.target.select()"
					@keyup.enter="setPassword">
				<NcButton
					:aria-label="t('cospend', 'Set project password')"
					@click="setPassword">
					<template #icon>
						<CheckIcon :size="20" />
					</template>
				</NcButton>
			</div>
		</div>
	</div>
</template>

<script>
import GoogleCirclesCommunitiesIcon from 'vue-material-design-icons/GoogleCirclesCommunities.vue'
import AccountIcon from 'vue-material-design-icons/Account.vue'
import AccountGroupIcon from 'vue-material-design-icons/AccountGroup.vue'
import CheckIcon from 'vue-material-design-icons/Check.vue'
import InformationVariantIcon from 'vue-material-design-icons/InformationVariant.vue'
import ClipboardCheckOutlineIcon from 'vue-material-design-icons/ClipboardCheckOutline.vue'
import LockIcon from 'vue-material-design-icons/Lock.vue'
import DeleteIcon from 'vue-material-design-icons/Delete.vue'
import PlusIcon from 'vue-material-design-icons/Plus.vue'
import MenuRightIcon from 'vue-material-design-icons/MenuRight.vue'
import MenuDownIcon from 'vue-material-design-icons/MenuDown.vue'
import TextBoxIcon from 'vue-material-design-icons/TextBox.vue'
import LinkVariantIcon from 'vue-material-design-icons/LinkVariant.vue'
import QrcodeIcon from 'vue-material-design-icons/Qrcode.vue'

import NcSelect from '@nextcloud/vue/dist/Components/NcSelect.js'
import NcAvatar from '@nextcloud/vue/dist/Components/NcAvatar.js'
import NcActions from '@nextcloud/vue/dist/Components/NcActions.js'
import NcActionButton from '@nextcloud/vue/dist/Components/NcActionButton.js'
import NcActionRadio from '@nextcloud/vue/dist/Components/NcActionRadio.js'
import NcActionInput from '@nextcloud/vue/dist/Components/NcActionInput.js'
import NcActionCheckbox from '@nextcloud/vue/dist/Components/NcActionCheckbox.js'
import NcActionLink from '@nextcloud/vue/dist/Components/NcActionLink.js'
import NcActionSeparator from '@nextcloud/vue/dist/Components/NcActionSeparator.js'
import NcModal from '@nextcloud/vue/dist/Components/NcModal.js'
import NcButton from '@nextcloud/vue/dist/Components/NcButton.js'
import NcDialog from '@nextcloud/vue/dist/Components/NcDialog.js'

import { getCurrentUser } from '@nextcloud/auth'
import { generateUrl, generateOcsUrl } from '@nextcloud/router'
import {
	showSuccess,
	showError,
} from '@nextcloud/dialogs'
import cospend from '../state.js'
import * as constants from '../constants.js'
import * as network from '../network.js'
import axios from '@nextcloud/axios'
import { Timer, hexToDarkerHex, getComplementaryColor } from '../utils.js'
import ClippyIcon from './icons/ClippyIcon.vue'

import QRCode from './QRCode.vue'

export default {
	name: 'SharingTabSidebar',

	components: {
		ClippyIcon,
		NcAvatar,
		NcActions,
		NcActionButton,
		NcActionRadio,
		NcActionInput,
		NcActionCheckbox,
		NcActionLink,
		NcActionSeparator,
		NcSelect,
		NcModal,
		NcButton,
		NcDialog,
		QRCode,
		QrcodeIcon,
		LockIcon,
		TextBoxIcon,
		DeleteIcon,
		PlusIcon,
		MenuDownIcon,
		MenuRightIcon,
		ClipboardCheckOutlineIcon,
		LinkVariantIcon,
		InformationVariantIcon,
		CheckIcon,
		AccountIcon,
		AccountGroupIcon,
		GoogleCirclesCommunitiesIcon,
	},

	props: {
		project: {
			type: Object,
			required: true,
		},
	},

	data() {
		return {
			constants,
			selectedSharee: null,
			sharees: [],
			guestLinkCopied: false,
			linkCopied: {},
			newGuestPassword: '',
			newPasswordReadonly: true,
			addingPublicLink: false,
			shareLinkQrcodeUrl: null,
			qrcodeColor: cospend.themeColorDark,
			// the svg api is dead, glory to the svg api
			qrcodeImageUrl: generateUrl('/apps/cospend/svg/cospend_square_bg?color=' + hexToDarkerHex(getComplementaryColor(cospend.themeColorDark)).replace('#', '')),
			showPasswordAccess: false,
			showOldLinkInfo: false,
		}
	},

	computed: {
		editionAccess() {
			return this.project.myaccesslevel >= constants.ACCESS.PARTICIPANT
		},
		myAccessLevel() {
			return this.project.myaccesslevel
		},
		shares() {
			return this.project.shares
		},
		linkShares() {
			return this.shares.filter((sh) => { return sh.type === constants.SHARE_TYPE.PUBLIC_LINK })
		},
		ugcShares() {
			return this.shares.filter((sh) => { return sh.type !== constants.SHARE_TYPE.PUBLIC_LINK })
		},
		projectId() {
			return this.project.id
		},
		isCurrentUser() {
			return (uid) => uid === getCurrentUser().uid
		},
		formatedSharees() {
			return this.unallocatedSharees.map(item => {
				return {
					user: item.id,
					manually_added: true,
					name: item.name,
					displayName: item.label,
					type: item.type,
					value: item.value,
					id: item.type + ':' + item.id,
				}
			})
		},
		// those with which the project is not shared yet
		unallocatedSharees() {
			return this.sharees.filter((sharee) => {
				let foundIndex
				if (sharee.type === constants.SHARE_TYPE.USER) {
					foundIndex = this.shares.findIndex((access) => {
						return access.userid === sharee.id && access.type === constants.SHARE_TYPE.USER
					})
				} else if (sharee.type === constants.SHARE_TYPE.GROUP) {
					foundIndex = this.shares.findIndex((access) => {
						return access.groupid === sharee.id && access.type === constants.SHARE_TYPE.GROUP
					})
				} else if (sharee.type === constants.SHARE_TYPE.CIRCLE) {
					foundIndex = this.shares.findIndex((access) => {
						return access.circleid === sharee.id && access.type === constants.SHARE_TYPE.CIRCLE
					})
				}
				if (foundIndex === -1) {
					return true
				}
				return false
			})
		},
		guestLink() {
			return window.location.protocol + '//' + window.location.host + generateUrl('/apps/cospend/loginproject/' + this.projectId)
		},
	},

	mounted() {
	},

	methods: {
		toggleShowPasswordAccess() {
			this.showPasswordAccess = !this.showPasswordAccess
		},
		canSetAccessLevel(level, access) {
			// i must be able to edit, have at least perms of the access, have at least same perms as what i want to set
			// and i can't edit myself
			return this.editionAccess && this.myAccessLevel >= access.accesslevel && this.myAccessLevel >= level
				&& (access.type !== constants.SHARE_TYPE.USER || !this.isCurrentUser(access.userid))
		},
		asyncFind(query) {
			this.query = query
			if (query === '') {
				this.sharees = []
				return
			}
			const url = generateOcsUrl('core/autocomplete/get', 2).replace(/\/$/, '')
			axios.get(url, {
				params: {
					format: 'json',
					search: query,
					itemType: ' ',
					itemId: ' ',
					shareTypes: [0, 1, 7],
				},
			}).then((response) => {
				this.sharees = response.data.ocs.data.map((s) => {
					const displayName = s.source === 'circles'
						? s.label
						: s.id !== s.label ? s.label + ' (' + s.id + ')' : s.label
					return {
						id: s.id,
						name: s.label,
						value: displayName,
						label: displayName,
						type: s.source === 'users'
							? constants.SHARE_TYPE.USER
							: s.source === 'groups'
								? constants.SHARE_TYPE.GROUP
								: constants.SHARE_TYPE.CIRCLE,
					}
				})
			}).catch((error) => {
				console.error(error)
			})
		},
		clickShareeItem() {
			this.addSharedAccess(this.selectedSharee)
		},
		addSharedAccess(sh) {
			this.addingPublicLink = true
			network.addSharedAccess(this.projectId, sh).then((response) => {
				const newShAccess = {
					accesslevel: constants.ACCESS.PARTICIPANT,
					type: sh.type,
					manually_added: sh.manually_added,
					password: sh.password,
				}
				newShAccess.id = response.data.id
				if (sh.type === constants.SHARE_TYPE.PUBLIC_LINK) {
					newShAccess.token = response.data.token
					this.copyLink(newShAccess)
				} else {
					newShAccess.name = response.data.name
					if (sh.type === constants.SHARE_TYPE.USER) {
						newShAccess.userid = sh.user
					} else if (sh.type === constants.SHARE_TYPE.GROUP) {
						newShAccess.groupid = sh.user
					} else if (sh.type === constants.SHARE_TYPE.CIRCLE) {
						newShAccess.circleid = sh.user
					}
				}
				cospend.projects[this.projectId].shares.push(newShAccess)
				this.selectedSharee = null
			}).catch((error) => {
				showError(
					t('cospend', 'Failed to add shared access')
					+ ': ' + (error.response?.data?.message || error.response?.request?.responseText),
				)
				console.error(error)
			}).then(() => {
				this.addingPublicLink = false
			})
		},
		clickAccessLevel(access, level) {
			network.setAccessLevel(this.projectId, access, level).then((response) => {
				access.accesslevel = level
			}).catch((error) => {
				showError(
					t('cospend', 'Failed to edit shared access level')
					+ ': ' + (error.response?.data?.message || error.response?.request?.responseText),
				)
				console.error(error)
			})
		},
		onPasswordCheck(access) {
			this.$set(access, 'password', '')
		},
		onPasswordUncheck(access) {
			this.savePassword(access, '')
		},
		submitPassword(access, e) {
			const password = e.target[0].value
			this.savePassword(access, password)
		},
		savePassword(access, password) {
			network.editSharedAccess(this.projectId, access, null, password).then((response) => {
				if (password === '') {
					this.$set(access, 'password', null)
				} else {
					this.$set(access, 'password', password)
				}
				showSuccess(t('cospend', 'Share link saved'))
			}).catch((error) => {
				showError(
					t('cospend', 'Failed to edit share link')
					+ ': ' + (error.response?.data?.message || error.response?.request?.responseText),
				)
				console.error(error)
			})
		},
		submitLabel(access, e) {
			const label = e.target[0].value
			network.editSharedAccess(this.projectId, access, label, null).then((response) => {
				this.$set(access, 'label', label)
				showSuccess(t('cospend', 'Share link saved'))
			}).catch((error) => {
				showError(
					t('cospend', 'Failed to edit share link')
					+ ': ' + (error.response?.data?.message || error.response?.request?.responseText),
				)
				console.error(error)
			})
		},
		clickDeleteAccess(access) {
			// to make sure the menu disappears
			this.$refs.shareWithList.click()
			network.deleteAccess(this.projectId, access).then((response) => {
				const index = this.shares.indexOf(access)
				this.shares.splice(index, 1)
			}).catch((error) => {
				showError(
					t('cospend', 'Failed to delete shared access')
					+ ': ' + (error.response?.data?.message || error.response?.request?.responseText),
				)
				console.error(error)
			})
		},
		generatePublicLink(access) {
			return window.location.protocol + '//' + window.location.host + generateUrl('/apps/cospend/s/' + access.token)
		},
		async copyLink(access) {
			const publicLink = this.generatePublicLink(access)
			try {
				await this.$copyText(publicLink)
				this.$set(this.linkCopied, access.id, true)
				// eslint-disable-next-line
				new Timer(() => {
					this.$set(this.linkCopied, access.id, false)
				}, 5000)
			} catch (error) {
				console.error(error)
				showError(t('cospend', 'Link could not be copied to clipboard.'))
			}
		},
		generateCospendLink(access) {
			return (window.location.protocol === 'http:' ? 'cospend+http://' : 'cospend://')
				+ window.location.host
				+ generateUrl('').replace('/index.php', '')
				+ access.token + '/' + encodeURIComponent(access.password || 'no-pass')
		},
		displayCospendLinkQRCode(access) {
			this.shareLinkQrcodeUrl = this.generateCospendLink(access)
		},
		closeQrcodeModal() {
			this.shareLinkQrcodeUrl = null
		},
		addLink() {
			this.addSharedAccess({
				type: constants.SHARE_TYPE.PUBLIC_LINK,
				password: null,
			})
		},
		setPassword() {
			if (this.newGuestPassword) {
				this.$emit('project-edited', this.projectId, this.newGuestPassword)
				this.newGuestPassword = ''
			} else {
				showError(t('cospend', 'Password should not be empty.'))
			}
		},
		async copyPasswordLink() {
			const guestLink = this.guestLink
			try {
				await this.$copyText(guestLink)
				this.guestLinkCopied = true
				// eslint-disable-next-line
				new Timer(() => {
					this.guestLinkCopied = false
				}, 5000)
			} catch (error) {
				console.debug(error)
				showError(t('cospend', 'Link could not be copied to clipboard.'))
			}
		},
		clickGuestAccessLevel(level) {
			network.setGuestAccessLevel(this.projectId, level).then((response) => {
				cospend.projects[this.projectId].guestaccesslevel = level
				showSuccess(t('cospend', 'Guest access level changed.'))
			}).catch((error) => {
				showError(
					t('cospend', 'Failed to edit guest access level')
					+ ': ' + (error.response?.data?.message || error.response?.request?.responseText),
				)
			})
		},
	},
}
</script>
<style scoped lang="scss">
.success {
	color: var(--color-success);
}

.add-public-link-line * {
	cursor: pointer;
}

.qrcode-modal-content {
	margin: 12px;
	.qrcode-wrapper {
		display: flex;
		flex-direction: column;
		align-items: center;
	}
	p {
		max-width: 400px;
		overflow-wrap: anywhere;
		user-select: text;
	}
}

.shareInput {
	width: 100%;

	.shareSelectOption {
		display: flex;
		align-items: center;
	}

	.multiselect-name {
		flex-grow: 1;
		margin-left: 10px;
		overflow: hidden;
		text-overflow: ellipsis;
	}
	.multiselect-icon {
		opacity: 0.5;
	}
}

.shareWithList {
	margin-bottom: 20px;
}

.shareWithList li {
	display: flex;
	align-items: center;
}

.username {
	padding: 12px 9px;
	flex-grow: 1;
}

.project-owner-label {
	opacity: .7;
}

.avatarLabel {
	padding: 6px
}

.avatardiv {
	background-color: #f5f5f5;
	border-radius: 16px;
	width: 32px;
	height: 32px;
}

.enterPassword {
	display: flex;
	align-items: center;
	input {
		flex-grow: 1;
	}
}

.avatardiv.link-icon {
	background-color: var(--color-primary);
	color: white;
	display: flex;
	align-items: center;
	padding: 6px 6px 6px 6px;
}

.passwordAccessSwitch {
	cursor: pointer;
	display: flex;
	margin-bottom: 16px;
	span {
		margin-right: 8px;
	}
}
</style>
