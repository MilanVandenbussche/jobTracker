<template>
    <div v-if="authenticated" class="row">
        <common-view />
    </div>
    <div v-else class="row">
        <login-view/>
    </div>
</template>
<script>
export default {
    name: 'Start View',
    computed: {
        authenticated() {
            return this.$store.state.authenticated;
        }
    },
    methods: {
        checkAuthentication() {
            //
        }
    },
    mounted() {
        // Check local storage
        if(localStorage.remember_token){
            axios.get('/api/token', {
                headers: {
                    Authorization: "Bearer " + localStorage.remember_token
                }
            }).then( r => {
                if(r.data.exists){
                    this.$store.commit('setAuthentication', true);
                }
            })
        }
    }
}
</script>
