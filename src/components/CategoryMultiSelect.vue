<template>
	<NcSelect
		:value="selectedCategoryItem"
		class="categoryMultiSelect"
		label="displayName"
		:disabled="disabled"
		:clearable="false"
		:placeholder="placeholder"
		:options="formattedOptions"
		@input="onCategorySelected" />
</template>

<script>
import NcSelect from '@nextcloud/vue/dist/Components/NcSelect.js'

export default {
	name: 'CategoryMultiSelect',

	components: {
		NcSelect,
	},

	props: {
		disabled: {
			type: Boolean,
			default: false,
		},
		placeholder: {
			type: String,
			required: true,
		},
		categories: {
			type: Array,
			required: true,
		},
		value: {
			type: Object,
			default: () => null,
		},
	},

	data() {
		return {}
	},

	computed: {
		formattedOptions() {
			return this.categories.map(c => {
				return {
					...c,
					displayName: c.icon + ' ' + c.name,
				}
			})
		},
		selectedCategoryItem() {
			return this.value
				? {
					...this.value,
					displayName: this.value.icon + ' ' + this.value.name,
				}
				: null
		},
	},

	methods: {
		onCategorySelected(selected) {
			this.$emit('input', selected)
		},
	},
}
</script>

<style scoped lang="scss">
.categoryMultiSelect {
	height: 48px;
}
</style>
