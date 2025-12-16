import { createRouter, createWebHistory } from 'vue-router';
import Login from '../views/Login.vue';
import Dashboard from '../views/Dashboard.vue';
import UsersIndex from '../views/Admin/Users/Index.vue';
import UsersForm from '../views/Admin/Users/Form.vue';
import RolesIndex from '../views/Admin/Roles/Index.vue';
import RolesForm from '../views/Admin/Roles/Form.vue';
import AccessesIndex from '../views/Admin/Accesses/Index.vue';
import AccessesForm from '../views/Admin/Accesses/Form.vue';
import { useAuthStore } from '../stores/auth';

const router = createRouter({
    history: createWebHistory(),
    routes: [
        { path: '/login', component: Login, name: 'Login' },
        { path: '/forgot-password', component: () => import('../views/ForgotPassword.vue'), name: 'ForgotPassword' },
        { path: '/reset-password/:token', component: () => import('../views/ResetPassword.vue'), name: 'ResetPassword' },
        {
            path: '/',
            component: () => import('../layouts/AppLayout.vue'),
            children: [
                { path: 'dashboard', component: Dashboard, name: 'Dashboard' },

                // Users
                { path: 'users', component: UsersIndex, name: 'Users', meta: { requiresAuth: true } },
                { path: 'users/create', component: UsersForm, name: 'UsersCreate', meta: { requiresAuth: true } },
                { path: 'users/:id/edit', component: UsersForm, name: 'UsersEdit', meta: { requiresAuth: true } },

                // Roles
                { path: 'roles', component: RolesIndex, name: 'Roles', meta: { requiresAuth: true } },
                { path: 'roles/create', component: RolesForm, name: 'RolesCreate', meta: { requiresAuth: true } },
                { path: 'roles/:id/edit', component: RolesForm, name: 'RolesEdit', meta: { requiresAuth: true } },

                // Accesses
                { path: 'accesses', component: AccessesIndex, name: 'Accesses', meta: { requiresAuth: true } },
                { path: 'accesses/create', component: AccessesForm, name: 'AccessesCreate', meta: { requiresAuth: true } },
                { path: 'accesses/:id/edit', component: AccessesForm, name: 'AccessesEdit', meta: { requiresAuth: true } },

                // Profile
                { path: 'profile', component: () => import('../views/Profile.vue'), name: 'Profile', meta: { requiresAuth: true } },

                // Settings
                { path: 'settings', component: () => import('../views/Admin/Settings/Index.vue'), name: 'Settings', meta: { requiresAuth: true } },

                // Inventory
                { path: 'inventory', component: () => import('../views/Admin/Inventory/Index.vue'), name: 'Inventory', meta: { requiresAuth: true } },

                // Inventory - Categories
                { path: 'inventory/categories', component: () => import('../views/Admin/Inventory/Categories/Index.vue'), name: 'CategoriesIndex', meta: { requiresAuth: true } },
                { path: 'inventory/categories/create', component: () => import('../views/Admin/Inventory/Categories/Form.vue'), name: 'CategoriesCreate', meta: { requiresAuth: true } },
                { path: 'inventory/categories/:id/edit', component: () => import('../views/Admin/Inventory/Categories/Form.vue'), name: 'CategoriesEdit', meta: { requiresAuth: true } },

                // Inventory - Locations
                { path: 'inventory/locations', component: () => import('../views/Admin/Inventory/Locations/Index.vue'), name: 'LocationsIndex', meta: { requiresAuth: true } },
                { path: 'inventory/locations/create', component: () => import('../views/Admin/Inventory/Locations/Form.vue'), name: 'LocationsCreate', meta: { requiresAuth: true } },
                { path: 'inventory/locations/:id/edit', component: () => import('../views/Admin/Inventory/Locations/Form.vue'), name: 'LocationsEdit', meta: { requiresAuth: true } },

                // Inventory - Rubros
                { path: 'inventory/rubros', component: () => import('../views/Admin/Inventory/Rubros/Index.vue'), name: 'RubrosIndex', meta: { requiresAuth: true } },
                { path: 'inventory/rubros/create', component: () => import('../views/Admin/Inventory/Rubros/Form.vue'), name: 'RubrosCreate', meta: { requiresAuth: true } },
                { path: 'inventory/rubros/:id/edit', component: () => import('../views/Admin/Inventory/Rubros/Form.vue'), name: 'RubrosEdit', meta: { requiresAuth: true } },

                // Inventory - Equipment
                { path: 'inventory/equipment/create', component: () => import('../views/Admin/Inventory/Equipment/Form.vue'), name: 'EquipmentCreate', meta: { requiresAuth: true } },
                { path: 'inventory/equipment/:id/edit', component: () => import('../views/Admin/Inventory/Equipment/Form.vue'), name: 'EquipmentEdit', meta: { requiresAuth: true } },

                // Interventions
                { path: 'interventions', component: () => import('../views/Admin/Interventions/Index.vue'), name: 'Interventions', meta: { requiresAuth: true } },
                { path: 'interventions/create', component: () => import('../views/Admin/Interventions/Form.vue'), name: 'InterventionsCreate', meta: { requiresAuth: true } },
                { path: 'interventions/:id/edit', component: () => import('../views/Admin/Interventions/Form.vue'), name: 'InterventionsEdit', meta: { requiresAuth: true } },

                { path: '', redirect: '/dashboard' },
            ]
        },
    ],
});

router.beforeEach(async (to, from, next) => {
    const auth = useAuthStore();

    if (to.meta.requiresAuth) {
        if (!auth.user) {
            try {
                await auth.fetchUser();
            } catch (e) {
                return next('/login');
            }
        }
        if (!auth.user) {
            return next('/login');
        }
    } else if (to.path === '/login' && auth.user) {
        return next('/dashboard');
    }

    next();
});

export default router;
