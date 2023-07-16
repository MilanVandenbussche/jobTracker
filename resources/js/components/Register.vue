<template>
    <form @submit.prevent="submitForm" class="d-flex flex-column gap-3">
        <div class="d-flex gap-3 align-self-stretch">
            <div class="form-floating flex-grow-1">
                <input v-model="firstname" type="text" class="form-control border-secondary-subtle" name="first_name" id="first_name"
                       placeholder="first_name">
                <label for="first_name" class="text-capitalize">first name</label>
            </div>
            <div class="form-floating flex-grow-1">
                <input v-model="lastname" type="text" class="form-control border-secondary-subtle" name="last_name" id="last_name"
                       placeholder="last_name">
                <label for="last_name" class="text-capitalize">last name</label>
            </div>
            <div>
                <select v-model="language" class="form-select border-secondary-subtle h-100" aria-label="Default select example">
                    <option value="0" selected hidden>Language</option>
                    <option value="NL">NL</option>
                    <option value="FR">FR</option>
                    <option value="EN">EN</option>
                </select>
            </div>
        </div>
        <div class="form-floating">
            <input v-model="email" type="email" class="form-control border-secondary-subtle" name="email" id="email"
                   placeholder="email">
            <label for="email" class="text-capitalize">email</label>
        </div>
        <div class="input-group">
            <div class="form-floating">
                <input v-model="password" :type="[showPassword ? 'text' : 'password']"
                       class="form-control border-end-0" :class="[password_match ? 'border-secondary-subtle' : 'border-danger']" name="password"
                       id="password" placeholder="password">
                <label for="password" class="text-capitalize">password</label>
            </div>
            <button type="button" @click="showPassword = !showPassword"
                    class="btn border border-start-0" :class="[password_match ? 'border-secondary-subtle' : 'border-danger']">
                <i class="far fa-eye text-secondary"></i>
            </button>
        </div>
        <div class="input-group">
            <div class="form-floating has-validation">
                <input v-model="password_confirm" :type="[showPasswordConfirm ? 'text' : 'password']"
                       class="form-control border-end-0" :class="[password_match ? 'border-secondary-subtle' : 'border-danger']" name="password-confirm"
                       id="password_confirm" placeholder="password">
                <label for="password-confirm" class="text-capitalize">confirm password</label>
            </div>
            <button type="button" @click="showPasswordConfirm = !showPasswordConfirm"
                    class="btn border border-start-0" :class="[password_match ? 'border-secondary-subtle' : 'border-danger']">
                <i class="far fa-eye text-secondary"></i>
            </button>
        </div>
        <div class="form-check form-switch">
            <input v-model="admin" class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault">
            <label class="form-check-label" for="flexSwitchCheckDefault">Admin</label>
        </div>
        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-outline-primary px-5 py-3">Continue</button>
            <p v-if="message.text" class="m-0" :class="[message.status === 'success' ? 'text-success' : 'text-danger']">
                {{ message.text }}
            </p>
        </div>
    </form>
</template>
<script>
export default {
    name: 'Login',
    data() {
        return {
            showPassword: false,
            showPasswordConfirm: false,

            password_match: true,
            // User data
            firstname: '',
            lastname: '',
            language: '0',
            email: '',
            password: '',
            password_confirm: '',
            admin: false,

            message: [
                status => null,
                text => null,
            ],
        }
    },
    methods: {
        submitForm(){
            if(this.password === this.password_confirm){
                axios.post('/api/register', {
                    first_name: this.firstname,
                    last_name: this.lastname,
                    language: this.language,
                    email: this.email,
                    password: this.password,
                    admin: this.admin,
                }).then( r => {
                    const  { status, remember_token } = r.data;
                    if(status === 'success'){
                        this.message.status = 'success';
                        this.$store.commit('setAuthentication', true);
                        localStorage.setItem('remember_token', remember_token);
                    }
                }).catch( e => {
                    this.message.status = 'error';
                    this.message.text = e.response.data.message;
                })
            }else{
                this.password_match = false;
            }
        }
    },
}
</script>
