export async function fetchApi(url, options = {}) {
    // Read on each request rather than on import, as the module is also loaded during server-side rendering
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const response = await fetch(url, {
        ...options,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            ...options.headers,
        },
        body: options.body ? JSON.stringify(options.body) : undefined,
    });

    if (!response.ok) {
        const error = new Error(`Request failed with status ${response.status}`);
        error.response = await response.json().catch(() => null);
        throw error;
    }

    return response.json();
}
