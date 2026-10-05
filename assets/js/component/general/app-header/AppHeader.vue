<template>

    <v-app-bar color="primary">
        <v-app-bar-nav-icon variant="text" @click.stop="onClickLeftDrawer"></v-app-bar-nav-icon>
        <v-toolbar-title>
            <slot name="header-title">
            </slot>
        </v-toolbar-title>
        <v-spacer></v-spacer>
        <v-app-bar-nav-icon :icon="mdiAccount" @click="onClickRightLeftDrawer"></v-app-bar-nav-icon>

    </v-app-bar>

    <v-navigation-drawer
        v-model="leftDrawer"
        :location="$vuetify.display.mobile ? 'top' : 'left'"
        temporary
    >
        <slot name="header-navigation">
        </slot>
    </v-navigation-drawer>


    <v-navigation-drawer
        v-model="rightDrawer"
        :location="$vuetify.display.mobile ? 'top' : 'right'"
        temporary
    >
        <slot name="user-navigation">
        </slot>
    </v-navigation-drawer>

</template>

<script setup lang="ts">

    import {mdiAccount} from "@mdi/js";

    const leftDrawer = defineModel('leftDrawer', {type: Boolean, default: false});
    const rightDrawer = defineModel('rightDrawer', {type: Boolean, default: false});

    const onClickLeftDrawer = () => {
        rightDrawer.value = false;
        leftDrawer.value = !leftDrawer.value;
    }

    const onClickRightLeftDrawer = () => {
        leftDrawer.value = false;
        rightDrawer.value = !rightDrawer.value;
    }

</script>
