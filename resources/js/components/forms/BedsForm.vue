<template>
  <div class="beds-form">
    <Table
      :headers="[{ label: 'Name', key: 'name' }, { label: 'Description', key: 'description' }]"
      :rows="beds"
      :actions="{ edit: true, delete: true }"
      @edit="(bed, i) => editBed(bed, i)"
      @delete="(bed, i) => deleteBed(i)">
      <template #header>
        <h3>Beds</h3>
        <Button classes="sm" @click="newBed">New</Button>
      </template>
      <template #prefix>
        <div v-if="current" class="bed-input">
          <h4>New Bed</h4>
          <div class="inputs-wrapper">
            <Input v-model="name" label="Name" maxlength="255" required/>
            <Input v-model="description" label="Description" maxlength="255"/>
            <Input :modelValue="image" type="file" label="Image" @change="e => image = e.target.value"/>
          </div>
          <div class="buttons-wrapper">
            <Button classes="sm" @click="done">{{ current.id || current.index !== undefined ? 'Update' : 'Add' }}</Button>
            <Button classes="sm" @click="$store.commit('beds/setCurrentBed', null)">Cancel</Button>
          </div>
        </div>
      </template>
    </Table>
  </div>
</template>

<script>
import { clone } from '../../utils/helpers'

export default {
  name: 'BedsForm',
  emits: ['done', 'delete'],
  computed: {
    beds () {
      return this.$store.state.locations.current.beds
    },
    current () {
      return this.$store.state.beds.current
    },
    name: {
      get () {
        return this.current.name
      },
      set (value) {
        this.$store.commit('beds/setCurrentBedName', value)
      }
    },
    description: {
      get () {
        return this.current.description
      },
      set (value) {
        this.$store.commit('beds/setCurrentBedDescription', value)
      }
    },
    image: {
      get () {
        return this.current.image
      },
      set (value) {
        this.$store.commit('beds/setCurrentBedImage', value)
      }
    }
  },
  methods: {
    newBed () {
      this.$store.commit('beds/setCurrentBed', { name: '', description: '', image: '' })
    },
    editBed (bed, i) {
      this.$store.commit('beds/setCurrentBed', { ...clone(bed), index: i })
    },
    deleteBed (i) {
      this.$emit('delete', i)
    },
    done () {
      if (this.name.length) {
        this.$emit('done', this.current)
        this.$store.commit('beds/setCurrentBed', null)
      }
    }
  }
}
</script>

<style scoped lang="scss">
h4 {
  margin: 0;
  margin-bottom: 1em;
}
.bed-input {
  margin-bottom: 1em;
  background-color: $grey-800;
  padding: 1em;
  :deep(input) {
    min-width: 350px;
    height: 35px;
    padding-left: 0.5em;
    background: $grey-700;
    color: $white;
    outline: none;
    border: 1px solid $grey-500;
    border-radius: 0.25em;
  }
}
</style>