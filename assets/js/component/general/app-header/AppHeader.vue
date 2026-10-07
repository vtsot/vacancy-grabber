<template>

    <v-app-bar color="primary">
        <v-app-bar-nav-icon
            v-if="userId"
            variant="text"
            @click.stop="onClickLeftDrawer"
        />

        <v-toolbar-title>
            <slot name="header-title">
            </slot>
        </v-toolbar-title>
        <v-spacer></v-spacer>

        <v-app-bar-nav-icon
            :icon="mdiAccount"
            @click="onClickRightLeftDrawer"
        />

    </v-app-bar>

    <v-navigation-drawer
        v-if="userId"
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

    import {onMounted, ref} from "vue";
    import {mdiAccount} from "@mdi/js";

    const userId = ref<string | null>(null);
    const leftDrawer = defineModel('leftDrawer', {type: Boolean, default: false});
    const rightDrawer = defineModel('rightDrawer', {type: Boolean, default: false});

    onMounted(() => {
        userId.value = document.documentElement.dataset.userId || null;
    })

    const onClickLeftDrawer = () => {
        rightDrawer.value = false;
        leftDrawer.value = !leftDrawer.value;
    }

    const onClickRightLeftDrawer = () => {
        leftDrawer.value = false;
        rightDrawer.value = !rightDrawer.value;
    }

</script>
