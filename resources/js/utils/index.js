import { Inertia } from "@inertiajs/inertia";
import { JWT_DATA } from "./constants";

export function isTokenInLocalStorage() {

    const token = localStorage.getItem(JWT_DATA);
    
    return token !== null && token !== '';
}

export async function validateOrRefreshToken() {

    const jwtData = localStorage.getItem(JWT_DATA);

    const {token, refreshToken} = JSON.parse(jwtData);

    try {
        // Vérifie si le token JWT est valide
        const isTokenValid = (token) => {
            if (!token) return false;

            const payload = JSON.parse(atob(token.split('.')[1]));
            const currentTime = Math.floor(Date.now() / 1000);

            return payload.exp > currentTime; // Vérifie si le token n'est pas expiré
        };

        // Si le token est encore valide, le retourner
        if (isTokenValid(token)) {
            return token;
        }

        // Si le token est expiré, essaye de le rafraîchir
        const response = await fetch(`/refresh-token`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Authorization: `Bearer ${refreshToken}`
            }
        });

        if (response.ok) {
            const data = await response.json();
            localStorage.setItem(JWT_DATA, JSON.stringify(data));
            return data.token; // Retourne le nouveau token
        } else {
            Inertia.visit('/');
            console.error('Failed to refresh token:', await response.text());
            return ''; // Retourne une chaîne vide si le refresh a échoué
        }
    } catch (error) {
        Inertia.visit('/');
        console.error('An error occurred:', error);
        return ''; // Retourne une chaîne vide en cas d'erreur
    }
}


