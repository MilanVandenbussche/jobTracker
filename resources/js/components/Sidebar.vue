<template>
        <section class="position-fixed col-2 vh-100 py-5 d-flex flex-column align-items-center position-relative shadow">
            <div class="d-flex flex-column align-items-center cursor-default">
                <h1 class="text-center fw-semibold align-self-center">JobTrackr</h1>
                <div class="border border-2 border-primary w-px-50 ms-5 me-n5 mt-n2"></div>
            </div>
            <div class="border border-secondary-subtle border-bottom-0 w-px-100 my-5"></div>
            <ul class="list-unstyled mx-5 d-flex flex-column align-items-center gap-3 mb-0 w-75">
                <li @click="changePage('dashboard')" class="w-100 cursor-pointer d-flex align-items-center fs-base fw-semibold" :class="[ currentPage === 'dashboard' ? 'text-primary gap-3': 'text-muted gap-2']">
                    <i class="fas fa-home"></i>
                    <span class="fs-1">Dashboard</span>
                </li>
                <li @click="changePage('users')" class="w-100 cursor-pointer d-flex align-items-center fs-base fw-semibold" :class="[ currentPage === 'users' ? 'text-primary gap-3': 'text-muted gap-2']">
                    <i class="fas fa-users"></i>
                    <span class="fs-1">Users</span>
                </li>
                <li @click="changePage('jobs')" class="w-100 cursor-pointer d-flex align-items-center fs-base fw-semibold" :class="[ currentPage === 'jobs' ? 'text-primary gap-3': 'text-muted gap-2']">
                    <i class="fas fa-briefcase"></i>
                    <span class="fs-1">Jobs</span>
                </li>
            </ul>
            <div class="position-absolute bottom-0 start-50 mb-5 translate-middle-x d-flex justify-content-center align-items-center gap-3 w-100">
                <div>
                    <img :src="userData ? userData.profile_picture : ''" :alt="userData ? userData.firstName + ' ' + userData.lastName : 'temp'" class="sq-40 img-fluid rounded-circle shadow" :title="userData ? userData.firstName + ' ' + userData.lastName : 'temp'">
                </div>
                <p class="fs-3 m-0 fw-bold">
                    {{ userData ? userData.firstName + ' ' + userData.lastName : ''}}
                </p>
                <div class="cursor-pointer" @click="signOut">
                    <i class="fas fa-sign-out"></i>
                </div>
            </div>
        </section>
</template>
<script>
export default {
    props: {
        currentPage: String,
        userData: Object,
    },
    methods: {
        changePage(page) {
            this.$store.commit('changePage', page);
        },
        signOut() {
            this.$store.dispatch('signOut');
        }
    },
}
</script>
