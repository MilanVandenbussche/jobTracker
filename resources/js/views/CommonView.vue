<template>
    <div class="row" v-if="!loading">
        <side-bar :user-data="userData" :current-page="currentPage" :key="int"></side-bar>
        <div class="col-10 ms-auto">
            <div class="row">
                <div class="col-12 col-md-10 offset-md-1 pt-5 position-relative vh-100">
                    <dashboard v-if="currentPage === 'dashboard'"></dashboard>
                    <users @updateAuthUser="getAuthUserData" v-if="currentPage === 'users'"></users>
                    <jobs v-if="currentPage === 'jobs'"></jobs>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: "Common View",
    data() {
        return {
            loading: true,
            userData: null,
        }
    },
    computed: {
        currentPage() {
            return this.$store.state.currentPage;
        },
    },
    methods: {
        async getAuthUserData() {
            try {
                const response = await axios.get('api/user', {
                    headers: {
                        Authorization: "Bearer " + localStorage.remember_token,
                    }
                });
                this.userData = response.data.user_data;
                this.loading = false;
            } catch (error) {
                console.error(error.response.data.message);
            }
        },
    },
    mounted() {
        this.getAuthUserData();
        this.$store.commit('changePage', 'dashboard');
    }
}
</script>
