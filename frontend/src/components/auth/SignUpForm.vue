<template>
    <div class="form">
        <h3
            class="title grey--text text--darken-1 mb-8 mt-6"
        >
            Welcome to Metrica!
        </h3>
        <VForm
            lazy-validation
            ref="form"
            v-model="valid"
        >
            <label
                class="caption grey--text"
            >
                Full name
            </label>
            <VTextField
                class="no-underline mt-1"
                solo
                type="text"
                name="name"
                v-model="newUser.name"
                :rules="nameRules"
            />
            <label
                class="caption grey--text"
            >
                Email
            </label>
            <VTextField
                class="no-underline mt-1"
                solo
                type="email"
                name="email"
                v-model="newUser.email"
                :rules="emailRules"
            />
            <div class="password-group">
                <label
                    class="caption grey--text"
                >
                    Password
                </label>
                <VTextField
                    class="no-underline mt-1 password"
                    solo
                    name="password"
                    autocomplete="new-password"
                    v-model="newUser.password"
                    :append-icon="show1 ? 'visibility' : 'visibility_off'"
                    :rules="passwordRules"
                    :type="show1 ? 'text' : 'password'"
                    @click:append="show1 = !show1"
                />
                <label
                    class="caption grey--text"
                >
                    Confirm password
                </label>
                <VTextField
                    class="no-underline mt-1 password"
                    solo
                    name="confirmPassword"
                    autocomplete="new-password"
                    v-model="newUser.confirmPassword"
                    :append-icon="show2 ? 'visibility' : 'visibility_off'"
                    :rules="confirmPasswordRules"
                    :type="show2 ? 'text' : 'password'"
                    @click:append="show2 = !show2"
                />
                <div class="btn-group">
                    <VBtn
                        class="mt-5"
                        min-width="100px"
                        color="primary"
                        :disabled="!valid"
                        @click="onSignUp"
                    >
                        {{ signUpText }}
                    </VBtn>
                </div>
                <div class="mt-3">
                    <span>
                        Already have an account?
                        <span class="nowrap">
                            Please
                            <RouterLink
                                class="forgot-password-link"
                                :to="{name: 'login'}"
                            >
                                sign in
                            </RouterLink>
                        </span>
                    </span>
                </div>
            </div>
        </VForm>
        <SocialAuth
            class="mt-4"
        />
    </div>
</template>

<script>
    import {mapActions} from 'vuex';
    import {SIGN_UP} from "@/store/modules/auth/types/actions";
    import {validateEmail} from '@/services/validation';
    import {validatePassword} from '@/services/validation';
    import { SHOW_SUCCESS_MESSAGE, SHOW_ERROR_MESSAGE } from "@/store/modules/notification/types/actions";
    import SocialAuth from "./SocialAuth";

    export default {
        components: {
            SocialAuth
        },
        data () {
            return {
                isLoading: false,
                show1: false,
                show2: false,
                newUser: {
                    name: '',
                    email: '',
                    password: '',
                    confirmPassword: '',
                },

                valid: false,
                nameRules: [
                    v => !!v || 'Field full name is required',
                    v => (v && v.length >= 5 && (v.split(" ").length - 1) >= 1) || 'Enter the correct information'
                ],
                emailRules: [
                    v => !!v || 'E-mail is required',
                    v => validateEmail(v) || 'E-mail must be valid',
                ],
                passwordRules: [
                    v => !!v || 'Password is required',
                    v => validatePassword(v) || 'Password must be equal or more than 8 characters'
                ],
                confirmPasswordRules: [
                    v => !!v || 'Password is required',
                    v => v === this.newUser.password || 'Password should match'
                ]
            };
        },
        methods: {
            ...mapActions('auth', {
                signUp: SIGN_UP
            }),
            ...mapActions('notification', {
                showSuccessMessage: SHOW_SUCCESS_MESSAGE,
                showErrorMessage: SHOW_ERROR_MESSAGE
            }),
            onSignUp () {
                if (this.$refs.form.validate()) {
                    this.isLoading = true;
                    this.signUp({
                        name: this.newUser.name,
                        email: this.newUser.email,
                        password: this.newUser.password,
                    }).then((email) => {
                        this.showSuccessMessage(`You have been successfully registered! We sent account confirmation
                         on your email ${email}. Please, check your email`);
                        this.$router.push({name: 'login'});
                    }).catch((error) => {
                        this.showErrorMessage(error);
                    }).finally(() => {
                        this.isLoading = false;
                    });
                }
            },
            onSignIn () {
                return this.$router.push({name: 'login'});
            },
        },
        computed: {
            signUpText() {
                return this.isLoading ? 'Processing...' : 'SIGN UP';
            }
        },
    };
</script>

<style lang="scss" scoped>
.nowrap {
    white-space: nowrap;
}

.v-btn {
    font-family: Gilroy;
    letter-spacing: 0.4px;
    text-transform: none;
    border-radius: 3px;

    +.start {
        background: #3C57DE;
    }
    +.login {
        background: #FFFFFF;
        border: 2px solid #3C57DE;
        box-sizing: border-box;
        border-radius: 3px;
        color: #3C57DE;
    }
}
.password {
    max-width: 80%;
}
.password-group {
    display: flex;
    flex-direction: column;
}

.form {
    width: 50%;
}

</style>