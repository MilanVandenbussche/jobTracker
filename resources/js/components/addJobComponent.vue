<template>
    <form @submit.prevent="addJob" @reset="this.$emit('cancel')" class="d-flex flex-column gap-3 mb-5">
        <div class="d-flex gap-3 align-items-center">
            <div class="form-check form-check-reverse form-switch">
                <label class="form-check-label" for="is_active">Active</label>
                <input class="form-check-input" type="checkbox" role="switch" id="is_active">
            </div>
            <div class="flex-grow-1 d-flex gap-3 align-items-center">
                <label for="publish_date" class="text-nowrap">Publish Date <sup class="text-primary">*</sup></label>
                <input type="datetime-local" name="publish_date" id="publish_date" class="form-control"
                       :class="['publish_date' in errors ? 'is-invalid' : '']">
                <span v-if="'publish_date' in errors" class="text-danger text-nowrap">
                        {{ this.errors['publish_date'].message }}
                </span>
            </div>
        </div>
        <fieldset>
            <legend>Tags <span class="fs-xs">( ctrl + click to select multiple )</span></legend>
            <select id="tags" name="tags[]" multiple class="px-2 w-100 form-select">
                <option :value="tag.id" v-for="tag in tags" class="text-capitalize px-3 py-2 border border-primary rounded my-2 select">{{ tag.name }}</option>
            </select>
        </fieldset>
        <fieldset>
            <legend>Job Title <sup class="text-primary">*</sup></legend>
            <div class="d-flex gap-3">
                <div v-for="language in languages" class="form-floating flex-grow-1">
                    <input :key="language.id" type="text"
                           :name="'job_title_' + language.language_code"
                           :id="'job_title_' + language.language_code"
                           class="form-control"
                           :class="['job_title_' + language.language_code in errors ? 'is-invalid' : '']"
                           :placeholder="'job_title_' + language.language_code">
                    <label :for="'job_title_' + language.language_code">{{ language.language_code }}</label>
                    <span v-if="'job_title_' + language.language_code in errors" class="text-danger">
                        {{ this.errors['job_title_' + language.language_code].message }}
                    </span>
                </div>
            </div>
        </fieldset>
        <div class="d-flex gap-3">
            <fieldset class="flex-grow-1">
                <legend>Company Description <sup class="text-primary">*</sup></legend>
                <div class="d-flex flex-column gap-3">
                    <div v-for="language in languages" class="form-floating flex-grow-1">
                        <textarea :key="language.id" type="text" :name="'job_company_' + language.language_code"
                                  :id="'job_company_' + language.language_code" class="form-control"
                                  :class="['job_company_' + language.language_code in errors ? 'is-invalid' : '']"
                                  :placeholder="'job_company_' + language.language_code"></textarea>
                        <label :for="'job_company_' + language.language_code">{{
                                language.language_code
                            }}</label>
                        <span v-if="'job_company_' + language.language_code in errors" class="text-danger">
                        {{ this.errors['job_company_' + language.language_code].message }}
                    </span>
                    </div>
                </div>
            </fieldset>
            <fieldset class="flex-grow-1">
                <legend>Job Description <sup class="text-primary">*</sup></legend>
                <div class="d-flex flex-column gap-3">
                    <div v-for="language in languages" class="form-floating flex-grow-1">
                        <textarea :key="language.id" type="text" :name="'job_description_' + language.language_code"
                                  :id="'job_description_' + language.language_code" class="form-control"
                                  :class="['job_description_' + language.language_code in errors ? 'is-invalid' : '']"
                                  :placeholder="'job_description_' + language.language_code"></textarea>
                        <label :for="'job_description_' + language.language_code">{{ language.language_code }}</label>
                        <span v-if="'job_company_' + language.language_code in errors" class="text-danger">
                        {{ this.errors['job_company_' + language.language_code].message }}
                    </span>
                    </div>
                </div>
            </fieldset>
        </div>
        <div class="d-flex gap-3">
            <fieldset class="flex-grow-1">
                <legend>Job Qualifications <sup class="text-primary">*</sup></legend>
                <div class="d-flex flex-column gap-3">
                    <div v-for="language in languages" class="form-floating flex-grow-1">
                        <textarea :key="language.id" type="text" :name="'job_qualifications_' + language.language_code"
                                  :id="'job_qualifications_' + language.language_code" class="form-control"
                                  :class="['job_qualifications_' + language.language_code in errors ? 'is-invalid' : '']"
                                  :placeholder="'job_qualifications_' + language.language_code"></textarea>
                        <label :for="'job_qualifications_' + language.language_code">{{
                                language.language_code
                            }}</label>
                        <span v-if="'job_qualifications_' + language.language_code in errors" class="text-danger">
                        {{ this.errors['job_qualifications_' + language.language_code].message }}
                    </span>
                    </div>
                </div>
            </fieldset>
            <fieldset class="flex-grow-1">
                <legend>Job Offer <sup class="text-primary">*</sup></legend>
                <div class="d-flex flex-column gap-3">
                    <div v-for="language in languages" class="form-floating flex-grow-1">
                        <textarea :key="language.id" type="text" :name="'job_offer_' + language.language_code"
                                  :id="'job_offer_' + language.language_code" class="form-control"
                                  :class="['job_offer_' + language.language_code in errors ? 'is-invalid' : '']"
                                  :placeholder="'job_offer_' + language.language_code"></textarea>
                        <label :for="'job_offer_' + language.language_code">{{ language.language_code }}</label>
                        <span v-if="'job_offer_' + language.language_code in errors" class="text-danger">
                        {{ this.errors['job_offer_' + language.language_code].message }}
                    </span>
                    </div>
                </div>
            </fieldset>
        </div>
        <div class="d-flex gap-3 justify-content-between align-items-start">
            <fieldset>
                <legend>Images</legend>
                <div v-if="!(tempUrls.length > 0)">
                    <input @change="queueFiles" class="visually-hidden" type="file" id="files" name="files[]" multiple>
                    <label for="files" class="d-flex gap-2 align-items-center">
                <span class="d-flex align-items-center justify-content-center sq-40 bg-primary rounded-circle ">
                    <i class="fas fa-plus text-white"></i>
                </span>
                        <span>Click here to add images</span>
                    </label>
                </div>
                <div v-else class="d-flex gap-3">
                    <div v-for="url in tempUrls" class="position-relative">
                        <img :src="url" alt="preview" class="sq-100 object-fit-cover">
                        <button @click.prevent="removeFile(url)"
                                class="border-0 position-absolute top-0 end-0 m-1 sq-20 bg-primary rounded-circle d-flex justify-content-center align-items-center">
                            <i class="fas fa-xmark text-white"></i>
                        </button>
                    </div>
                </div>
            </fieldset>
            <div class="d-flex gap-2 align-self-end">
                <button type="reset" class="btn btn-outline-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    Save
                    <i v-if="!saving" class="fas fa-save"></i>
                    <span v-if="saving" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                </button>
            </div>
        </div>
    </form>
</template>
<script>
export default {
    name: 'addJob',
    emits: ["success", "cancel"],
    data() {
        return {
            languages: [],
            tags: [],
            files: [],
            tempUrls: [],
            errors: {},
            saving: false,
        }
    },
    methods: {
        async getTags(){
            try {
                const response = await axios.get('/api/jobs/tags', {});
                if(response.data.tags){
                    this.tags = response.data.tags;
                }
            }catch(e){
                console.error(e.response.data.message);
            }
        },
        async getLanguages() {
            try {
                const response = await axios.get('/api/languages/all', {})
                if(response.data.languages){
                    this.languages = response.data.languages;
                }
            }catch(e) {
                console.error(e.response.data.message);
            }
        },
        queueFiles(e) {
            Array.from(e.target.files).forEach((file) => {
                this.files.push(file);
                this.tempUrls.push(URL.createObjectURL(file));
            });
        },
        removeFile(url) {
            let index = this.tempUrls.findIndex(tempUrl => tempUrl === url);
            this.files.splice(index, 1);
            this.tempUrls.splice(index, 1);
        },
        async addJob() {
            this.errors = {};
            if (!document.getElementById('publish_date').value) {
                this.errors['publish_date'] = {"message": "This field is required"};
            }
            const fields = ["job_title", "job_company", "job_qualifications", "job_offer"];
            let data = {};
            this.languages.forEach((language) => {
                let formData = {};
                fields.forEach((field) => {
                    let value = document.getElementById(field + "_" + language.language_code).value;
                    if (value) {
                        formData[field] = value;
                    } else {
                        this.errors[field + "_" + language.language_code] = {"message": "This field is required"};
                    }
                });
                data[language.id] = formData;
            })
            if(Object.keys(this.errors).length === 0){
                this.saving = true;
                let formData = new FormData();
                formData.append("is_active", document.getElementById('is_active').checked);
                formData.append("publish_date", document.getElementById('publish_date').value);
                formData.append("data", JSON.stringify(data));
                this.files.forEach((file) => {
                    formData.append("images[]", file);
                });
                const selectedTagIds = Array.from(document.getElementById('tags').selectedOptions)
                    .map(option => option.value);
                formData.append("selectedTagIds", selectedTagIds);
                try {
                    const response = await axios.post('/api/jobs/create', formData, {
                        headers: {
                            Authorization: "Bearer " + localStorage.remember_token,
                            "Content-Type": "multipart/form-data",
                        },
                    });
                    if(response.data.status === "success"){
                        this.$emit("success");
                    }
                } catch (error) {
                    console.error(error.response.data);
                } finally {
                    this.saving = true;
                }

            }
        }
    },
    async mounted() {
        await this.getLanguages();
        await this.getTags();
    },
}
</script>
