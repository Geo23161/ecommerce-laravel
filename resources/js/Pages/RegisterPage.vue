<script setup lang="ts">
import { router as Inertia } from '@inertiajs/vue3'
import { ref, computed, onMounted } from 'vue'
import GlobalLoader from './components/GlobalLoader.vue'
import { JWT_DATA } from '../utils/constants'

const email = ref('')
const password = ref('')
const confirmPassword = ref('')
const userType = ref('buyer')
const kkiapayId = ref('')
const isLoading = ref(false)

const showKkiapayField = computed(() => userType.value === 'vendor')

const handleSubmit = async () => {
    if (confirmPassword.value != password.value) {
        alert('Les mots de passe doivent etre les memes.');
        return;
    }
    isLoading.value = true;
    console.log({
        email: email.value,
        password: password.value,
        kkiapayId: kkiapayId.value,
        type: localStorage.getItem('choice') ?? 'buyer'
    });
    try {
        const resp = await axios.post('/api/register', {
        email: email.value,
        password: password.value,
        kkiapayId: kkiapayId.value,
        type: localStorage.getItem('choice') ?? 'buyer'
    });
    if (resp.status.toString().startsWith('2')) {

        localStorage.setItem(JWT_DATA, JSON.stringify(resp.data));
        Inertia.visit(`/${localStorage.getItem('choice') ?? 'buyer'}`);

    }
    else {
        alert("L'Email a deja ete utilise.")
    }
    } catch (e) {
        
        alert("L'Email a deja ete utilise.")
        console.log(e);
    }
    isLoading.value = false;
}

onMounted(() => {
    userType.value = localStorage.getItem('choice');
});

const goToLogin = () => {
    Inertia.visit('/login');
}
</script>

<template>
    <div>
        <GlobalLoader :is-loading="isLoading" />
        <div class="auth-container">
            <h1>Inscription</h1>
            <form @submit.prevent="handleSubmit" class="auth-form">
                <div class="form-group">
                    <label for="userType">Type de compte</label>
                    <select disabled id="userType" v-model="userType" required class="form-select">
                        <option value="buyer">Acheteur</option>
                        <option value="vendor">Vendeur</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" v-model="email" required placeholder="votre@email.com">
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" v-model="password" required placeholder="Votre mot de passe">
                </div>

                <div class="form-group">
                    <label for="confirmPassword">Confirmer le mot de passe</label>
                    <input type="password" id="confirmPassword" v-model="confirmPassword" required
                        placeholder="Confirmez votre mot de passe">
                </div>

                <div v-if="showKkiapayField" class="form-group">
                    <label for="kkiapayId">ID Kkiapay</label>
                    <input type="text" id="kkiapayId" v-model="kkiapayId" required placeholder="Votre ID Kkiapay">
                </div>

                <button type="submit" class="submit-button">
                    S'inscrire
                </button>

                <p class="auth-switch">
                    Déjà inscrit ?
                    <a href="#" @click.prevent="goToLogin">Se connecter</a>
                </p>
            </form>
        </div>
    </div>

</template>