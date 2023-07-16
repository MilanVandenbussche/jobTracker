<template>
        <form @submit.prevent="submitForm" class="d-flex flex-column gap-3">
            <div class="form-floating has-validation">
                <input v-model="email" type="email" class="form-control" :class="[error != null ? 'is-invalid' : 'border-secondary-subtle']" name="email" id="email" placeholder="email" autocomplete="email">
                <label for="email" class="text-capitalize">Email</label>
            </div>
            <div class="input-group">
                <div class="form-floating has-validation">
                    <input v-model="password" :type="[showPassword ? 'text' : 'password']" class="form-control border-end-0" :class="[error != null ? 'is-invalid' : 'border-secondary-subtle']" name="password"
                           id="password" placeholder="password" autocomplete="current_password">
                    <label for="password" class="text-capitalize">password</label>
                </div>
                <button type="button" @click="showPassword = !showPassword" class="btn border border-start-0" :class="[error != null ? 'border-danger' : 'border-secondary-subtle']">
                    <i class="far fa-eye" :class="[error != null ? 'text-danger' : 'text-secondary']"></i>
                </button>
            </div>
            <div class="d-flex align-items-center gap-3">
                <button type="submit" class="btn btn-outline-primary px-5 py-3">Continue</button>
                <p  v-if="error" class="m-0 text-danger">{{ error }}</p>
            </div>
        </form>
</template>
<script>
export default {
    name: 'Login',
    emits: ["login"],
    data() {
        return {
            email: '',
            password: '',
            error: null,
            showPassword: false,
        }
    },
    methods: {
        submitForm(){
            axios.post('/api/login', {
                email: this.email,
                password: this.password
            }).then( r => {
                const { status, remember_token, admin, id } = r.data;
                if(status === 'success'){
                    this.$store.commit('changePage', 'dashboard');
                    this.error = null;
                    this.$store.commit('setAuthentication', true);
                    localStorage.setItem('remember_token', remember_token);
                    localStorage.setItem('user_id', String(id));
                    localStorage.setItem('admin', String(!!admin));
                }
            }).catch( e => {
                if(e.response.data.status === "failure"){
                    this.error = e.response.data.message;
                }
            })
        }
    },
}
</script>
