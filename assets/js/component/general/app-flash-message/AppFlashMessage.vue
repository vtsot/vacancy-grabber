<template>

    <v-snackbar-queue
        ref="flash-message-queue"
        v-model="flashMessageStore.messages"
        :location="props.location as string"
        :timeout="props.timeout"
        :total-visible="props.totalVisible"
        transition="bouncy-slide-auto"
        closable
        contained
        display-strategy="overflow"
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

    <div v-if="props.example" class="d-flex ga-2">
        <v-btn
            v-for="color in colors"
            :color="color"
            @click="flashMessageStore.createFlash({text: color, color: color, location: null})"
        >
            {{ color }}
        </v-btn>
    </div>
</template>

<script setup lang="ts">

    import {ref,} from "vue";
    import {useAppFlashMessageStore} from "./store";

    const props = defineProps({
        timeout: {type: Number, default: 7000},
        location: {type: String, default: 'top end'},
        totalVisible: {type: Number, default: 5},
        example: {type: Boolean, default: true},
    });

    const flashMessageStore = useAppFlashMessageStore();

    const colors = ref([
        'success',
        'error',
        'warning',
        'info',
        'primary',
        'secondary',
        'accent',
        'background',
        'surface',
    ]);

</script>
