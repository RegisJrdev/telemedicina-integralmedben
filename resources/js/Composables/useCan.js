// composables/useCan.js
import { usePage } from "@inertiajs/vue3";

export function useCan() {
    const page = usePage();

    return (permission) => {
        return page.props.auth.user?.can.includes(permission) ?? false;
    };
}
