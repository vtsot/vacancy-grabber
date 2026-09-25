<template>

    <v-snackbar-queue
        ref="flash-message-queue"
        v-model="flashStore.items"
        :absolute="props.absolute"
        :timeout="props.timeout"
        :total-visible="props.totalVisible"
        transition="bouncy-slide-auto"
    >
        <template v-slot:actions="{ item, props }">
            <v-icon-btn
                v-if="!item.vertical"
                aria-label="Close"
                icon="$close"
                size="small"
                variant="text"
                v-bind="props"
            ></v-icon-btn>
            <v-btn
                v-else
                text="Close"
                variant="text"
                v-bind="props"
            ></v-btn>

        </template>
    </v-snackbar-queue>

    <v-card-text v-if="props.example" class="mt-5">
        <v-divider class="my-2"/>

        <div>Flash:</div>
        <v-btn text="Clear" @click="flashStore.clear()"/>
        <v-btn color="default" text="add" @click="flashStore.add({title: 'title', text: 'text'})"/>
        <v-btn color="info" text="add Info" @click="flashStore.addInfo({title: 'title', text: 'text'})"/>
        <v-btn color="success" text="add Success" @click="flashStore.addSuccess({title: 'title', text: 'text'})"/>
        <v-btn color="warning" text="add Warning" @click="flashStore.addWarning({title: 'title', text: 'text'})"/>
        <v-btn color="error" text="add Error" @click="flashStore.addError({title: 'title', text: 'text'})"/>

        <v-divider class="my-2"/>

        items: {{ flashStore.items }}
        <v-divider class="my-2"/>
    </v-card-text>
</template>

<script setup lang="ts">

    import {FLASH_TIMEOUT, useFlashStore} from "./store";

    const props = defineProps({
        example: {type: Boolean, default: false},
        absolute: {type: Boolean, default: false},
        timeout: {type: Number, default: FLASH_TIMEOUT},
        location: {type: String, default: 'top end'},
        totalVisible: {type: Number, default: 10},
    });

    const flashStore = useFlashStore();

</script>
