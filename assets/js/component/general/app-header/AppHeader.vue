<template>

    <v-app-bar color="primary">
        <v-app-bar-nav-icon variant="text" @click.stop="drawer = !drawer"></v-app-bar-nav-icon>
        <v-toolbar-title>
            <slot name="header-title">
            </slot>
        </v-toolbar-title>
    </v-app-bar>

    <v-navigation-drawer
        v-model="drawer"
        :location="$vuetify.display.mobile ? 'top' : undefined"
        temporary
    >
        <slot name="header-navigation">
        </slot>
<!--        <v-list-item title="My Application" subtitle="Vuetify"></v-list-item>-->
<!--        <v-divider></v-divider>-->
<!--        <v-list-item link title="List Item 1"></v-list-item>-->
<!--        <v-list-item link title="List Item 2"></v-list-item>-->
<!--        <v-list-item link title="List Item 3"></v-list-item>-->

    </v-navigation-drawer>

</template>

<script>
    export default {
        data: () => ({
            drawer: false,
            // items: [
            //     {
            //         title: 'Sources',
            //         value: 'sources',
            //         href: '/source',
            //     },
            //     {
            //         title: 'Grab Logs',
            //         value: 'grab-log',
            //     },
            //     {
            //         title: 'Companies',
            //         value: 'companies',
            //         href: '/company',
            //     },
            //     {
            //         title: 'Vacancies',
            //         value: 'Vacancies',
            //     },
            //     {
            //         title: 'Notes',
            //         value: 'notes'
            //     },
            // ],
        }),

        // watch: {
        //     group() {
        //         this.drawer = false
        //     },
        // },

        // log: () => {
        //     console.log('log')
        // }
    }
</script>
