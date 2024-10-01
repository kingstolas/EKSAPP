<template>
  <div class="h-screen p-20 flex flex-col justify-between">
    <div>
      <h1 class="text-3xl font-bold ml-44">Bloc de notas</h1>
    </div>
    <div class="flex flex-col items-center w-full h-full pt-10">
      <div class="w-3/4 h-full rounded-lg">
        <div class="flex px-3 py-3.5 justify-end">
          <UButton icon="i-heroicons-pencil-square" label="Crear Nota" @click="isOpen = true" />
        </div>
        <div>
          <ul>
            <li v-for="note in notes" class="border-b p-5 flex justify-between cursor-pointer">
              <p class="w-2/3">{{ note.data }}</p>
              <p>{{ formatDate(note.created_at) }}</p>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <UModal v-model="isOpen">
      <UCard>
        <template #header>
          <div class="flex items-center justify-between">
            <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white">
              Nueva nota
            </h3>
            <UButton color="gray" variant="ghost" icon="i-heroicons-x-mark-20-solid" class="-my-1"
              @click="isOpen = false" />
          </div>
        </template>
        <UTextarea v-model="value" />
        <div class="w-full flex flex-col items-end mt-8">
          <UButton color="gray" label="Guardar" :loading="isLoading" @click="addNote()" />
        </div>
      </UCard>
    </UModal>
    <UNotifications />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import dayjs from 'dayjs';
import { useRuntimeConfig } from '#app';

interface Note {
  data: string;
  created_at: Date;
}

const value = ref('');
const isLoading = ref(false);
const isOpen = ref(false);
const notes = ref<Note[]>([]);
const toast = useToast();


onMounted(async () => {
  await fetchNotes();

});

function formatDate(date: Date) {
  return dayjs(date).format('DD/MM/YYYY');
}

const fetchNotes = async () => {
  try {
    const config = useRuntimeConfig();
    const apiBase = config.public.apiBase;

    console.log("Fetching notes from:", apiBase);

    const response = await fetch(`${apiBase}/notes`);

    if (!response.ok) {
      throw new Error('Network response was not ok');
    }

    const data = await response.json();
    notes.value = data;
  } catch (error) {
    toast.add({
      id: 'error_charging',
      title: 'Error',
      description: 'No se pudieron cargar las notas',
      timeout: 0,
    })
  }
};

const addNote = async () => {
  if (value.value.trim()) {
    isLoading.value = true;
    try {
      const config = useRuntimeConfig();
      const apiBase = config.public.apiBase;

      const response = await fetch(`${apiBase}/notes`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify({
          data: value.value.trim(),
        }),
      });

      if (response.ok) {
        const newNote: Note = {
          data: value.value.trim(),
          created_at: new Date,
        };

        notes.value.unshift(newNote);
        value.value = '';
        isOpen.value = false;
      }
    } catch (error) {
      toast.add({
        id: 'error_creating',
        title: 'Error',
        description: 'No se pudo crear la nota',
        timeout: 0,
      })
      console.error('Error adding note:', error);
    }
    finally {
      isLoading.value = false;
      isOpen.value = false;
    }
  }
};



</script>
