class AuthHandler {
    constructor(baseUrl) {
        this.baseUrl = baseUrl;
    }

    async login(username, password) {
        const response = await fetch(`${this.baseUrl}/wp-json/wcr/v1/auth/jwt_token`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ username, password }),
        });

        const data = await response.json();
        if (data.success) {
            localStorage.setItem('token', data.token);
        }

        return data;
    }

    async verifyToken() {
        const token = localStorage.getItem('token');
        if (!token) return { success: false };

        const response = await fetch(`${this.baseUrl}/wp-json/wcr/v1/auth/verify_jwt_token`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ token }),
        });

        const data = await response.json();
        return data;
    }
}

// ✅ Expose to global scope
window.AuthHandler = AuthHandler;
