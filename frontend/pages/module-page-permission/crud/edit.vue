<template>
	<form-edit with-helpdesk>
		<template v-slot:default="{ record }">
			<v-card-text>
				<v-row dense>
					<v-col cols="12">
						<v-text-field
							label="Name"
							v-model="record.name"
							hide-details
						></v-text-field>
					</v-col>

					<v-col cols="12">
						<v-text-field
							label="Slug"
							v-model="record.slug"
							hide-details
						></v-text-field>
					</v-col>
				</v-row>
			</v-card-text>
		</template>
	</form-edit>
</template>

<script>
export default {
	name: "system-role-edit",
};
</script>
