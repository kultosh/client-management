<template>
    <div v-if="show" class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5)">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Client</h5>
                    <button type="button" class="btn-close" @click="$emit('close')"></button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="handleSubmit">
                        <div class="mb-3">
                            <label for="companyName" class="form-label">Company Name</label>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="companyName"
                                v-model="formData.company_name"
                                required
                            >
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input 
                                type="email" 
                                class="form-control" 
                                id="email"
                                v-model="formData.email"
                                required
                            >
                        </div>
                        <div class="mb-3">
                            <label for="phoneNumber" class="form-label">Phone Number</label>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="phoneNumber"
                                v-model="formData.phone_number"
                                required
                            >
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="$emit('close')">Cancel</button>
                    <button 
                        type="button" 
                        class="btn btn-primary" 
                        @click="handleSubmit"
                        :disabled="updating"
                    >
                        <span v-if="updating" class="spinner-border spinner-border-sm me-2" role="status">
                        <span class="visually-hidden">Loading...</span>
                        </span>
                        {{ updating ? 'Updating...' : 'Update Client' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'EditClientModal',
    props: {
        show: {
            type: Boolean,
            default: false
        },
        client: {
            type: Object,
            default: null
        },
        updating: {
            type: Boolean,
            default: false
        }
    },
    emits: ['close', 'update-client'],
    data() {
        return {
            formData: {
                company_name: '',
                email: '',
                phone_number: ''
            }
        }
    },
    watch: {
        client: {
            handler(newClient) {
                if (newClient) {
                this.formData = {
                    company_name: newClient.company_name || '',
                    email: newClient.email || '',
                    phone_number: newClient.phone_number || ''
                }
                }
            },
            immediate: true
        }
    },
    methods: {
        handleSubmit() {
            this.$emit('update-client', this.formData)
        }
    }
}
</script>

<style scoped>
.modal {
  background-color: rgba(0,0,0,0.5);
}
</style>