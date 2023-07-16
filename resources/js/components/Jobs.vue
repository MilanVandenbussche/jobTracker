<template>
    <h1 class="fs-6xl">Jobs</h1>
    <div class="row">
        <jobs-component v-if="!addJobBool && !editJobBool" @addJob="addJobBool = true" @editJob="editJob"></jobs-component>
        <add_job-component @success="addJobBool = false" @cancel="addJobBool = false" v-if="addJobBool"></add_job-component>
    </div>
</template>
<script>
export default {
    name:'dashboard',
    data() {
        return {
            addJobBool: false,
            jobId: null,
        }
    },
    methods: {
        //
    },
    async mounted() {
        //
    },
}
</script>
