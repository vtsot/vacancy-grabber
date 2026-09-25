<template>

    <v-card-text v-if="alertStore.items" class="alert-container">
        <v-alert
            v-for="(item, key) in alertStore.items"
            :key="key"
            :color="item.color"
            :icon="item.icon as string"
            :title="item.title"
            :text="item.text"
            :closable="item.closable as boolean"
            class="mb-2"
            variant="tonal"
        />
    </v-card-text>

    <v-card-text v-if="props.example" class="mt-5">
        <v-divider class="my-2"/>

        <div>Alert:</div>
        <v-btn text="Clear" @click="alertStore.clear()"/>
        <v-btn color="default" text="add" @click="alertStore.add({title: 'title', text: 'text'} as AlertInterface)"/>
        <v-btn color="info" text="add Info" @click="alertStore.addInfo({title: 'title', text: 'text'} as AlertInterface)"/>
        <v-btn color="success" text="add Success" @click="alertStore.addSuccess({title: 'title', text: 'text'} as AlertInterface)"/>
        <v-btn color="warning" text="add Warning" @click="alertStore.addWarning({title: 'title', text: 'text'} as AlertInterface)"/>
        <v-btn color="error" text="add Error" @click="alertStore.addError({title: 'title', text: 'text'} as AlertInterface)"/>

        <v-divider class="my-2"/>

        items: {{ alertStore.items }}
        <v-divider class="my-2"/>
    </v-card-text>
</template>

<script setup lang="ts">
    import {AlertInterface, useAlertStore} from "./store";

    const props = defineProps({
        example: {type: Boolean, default: false},
    });

    const alertStore = useAlertStore();

</script>

<!--<style scoped>-->
<!--    .alert-container {-->
<!--        position: fixed;-->
<!--        top: 20px;-->
<!--        right: 20px;-->
<!--        z-index: 9999;-->
<!--        width: 350px;-->
<!--    }-->
<!--</style>-->
