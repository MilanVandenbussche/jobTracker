<template>
<h1 class="fs-6xl">Dashboard</h1>
    <div class="row g-3">
        <div class="col-12 col-md-6">
            <div class="border border-primary w-100 p-3 rounded-3">
                <h2 class="fs-5xl m-0">Active Users</h2>
                <p class="m-0 fs-3xl">{{ usersCount }}</p>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="border border-primary w-100 p-3 rounded-3">
                <h2 class="fs-5xl m-0">Active Jobs</h2>
                <p class="m-0 fs-3xl">{{ jobsCount }}</p>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name:'dashboard',
    data() {
        return {
            usersCount: null,
            jobsCount: null,
        }
    },
    methods: {
        async getUserCount(){
            try {
                const response = await axios.get('/api/user/count', {});
                console.log(response)
                if(response.data.usersCount){
                    this.usersCount = response.data.usersCount;
                }
            }catch(e) {
                console.error(e.message);
            }
        },

        async getJobsCount(){
            try {
                const response = await axios.get('/api/jobs/count', {});
                if(response.data.jobsCount){
                    this.jobsCount = response.data.jobsCount;
                }
            }catch(e) {
                console.error(e.message);
            }
        },
    },
    async mounted(){
        await this.getUserCount();
        await this.getJobsCount();
    }
}
</script>
