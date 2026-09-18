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
    </v-navigation-drawer>

</template>

<script setup>

    const drawer = defineModel('drawer', { type: Boolean, default: false });

</script>
