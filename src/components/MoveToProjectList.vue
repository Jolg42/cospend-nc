<template>
	<div class="project-list">
		<h2>
			{{ t('cospend', 'Move bill "{bill}" to a different project:', { bill: bill.what }) }}
		</h2>
		<ul>
			<NcListItem v-for="(project) in candidateTargetProjects"
				:key="project.id"
				:title="project.name"
				:name="project.name"
				@click="onProjectClicked(project)" />
		</ul>
		<NcEmptyContent v-if="cospend.projects.length === 1 && cospend.projects[projectId]"
			:name="t('cospend', 'Only one project available, in which this bill already exists')"
			:title="t('cospend', 'Only one project available, in which this bill already exists')" />
		<NcEmptyContent v-else-if="cospend.projects.length === 0"
			:name="t('cospend', 'No projects found')"
			:title="t('cospend', 'No projects found')" />
	</div>
</template>
<script>
import NcListItem from '@nextcloud/vue/dist/Components/NcListItem.js'
import cospend from '../state.js'
import * as network from '../network.js'
import { showError, showSuccess } from '@nextcloud/dialogs'

export default {
	name: 'MoveToProjectList',
	components: {
		NcListItem,
	},
	props: {
		bill: {
			type: Object,
			required: true,
		},
		projectId: {
			type: String,
			required: true,
		},
	},
	data() {
		return {
			cospend,
		}
	},
	computed: {
		candidateTargetProjects() {
			// only those with a member named like the bill payer
			const payerName = cospend.members[this.projectId][this.bill.payer_id].name
			const projects = {}
			Object.values(cospend.projects).forEach(p => {
				if (p.id !== this.projectId && this.projectHasMemberNamed(p.id, payerName)) {
					projects[p.id] = cospend.projects[p.id]
				}
			})
			return projects
		},
	},
	created() {
	},
	methods: {
		projectHasMemberNamed(projectId, nameQuery) {
			const foundMember = Object.values(cospend.members[projectId]).find(m => {
				return m.name === nameQuery
			})
			return !!foundMember
		},
		onProjectClicked(project) {
			network.moveBill(this.projectId, this.bill.id, project.id).then(res => {
				showSuccess(t('cospend', 'Bill moved to "{project}" successfully', { project: project.name }))
				this.$emit('item-moved', res.data, project.id)
			}).catch(error => {
				console.error(error)
				showError(
					t('cospend', 'Failed to move bill')
					+ ': ' + (error.response?.data?.message || error.response?.request?.responseText),
				)
			})
		},
	},
}
</script>

<style scoped lang="scss">
.project-list {
	padding: 12px;
	width: 92%;
}
</style>
