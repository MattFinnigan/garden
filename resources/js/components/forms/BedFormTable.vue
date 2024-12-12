<template>
  <div class="beds-form">
    <Table
      :headers="[{ label: 'Name', key: 'name' }, { label: 'Description', key: 'description' }, { label: 'Size', key: 'size' }]"
      :rows="bedsMapped"
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
            <Input v-model.number="l" type="number" label="Length (cm)" :required="w"/>
            <Input v-model.number="w" type="number" label="Width (cm)" :required="l"/>
            <Input v-model="description" label="Description" maxlength="255"/>
            <Input :modelValue="image" type="file" label="Image" @change="e => image = e.target.value"/>
          </div>
          <p v-if="errors" class="error">{{ errors }}</p>
          <div class="buttons-wrapper">
            <Button classes="sm" @click="done">{{ current.id || current.index !== undefined ? 'Update' : 'Add' }}</Button>
            <Button classes="sm" @click="() => { errors = false; $store.commit('beds/setCurrentBed', null) }">Cancel</Button>
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
  data () {
    return {
      errors: false
    }
  },
  computed: {
    beds () {
      return this.$store.state.locations.current.beds
    },
    bedsMapped () {
      return this.beds.map(bed => {
        return {
          ...bed,
          size: bed.l ? `${bed.l / 100}x${bed.w / 100}m` : 'N/A'
        }
      })
    },
    current () {
      return this.$store.state.beds.current
    },
    location () {
      return this.$store.state.locations.current
    },
    name: {
      get () {
        return this.current.name
      },
      set (value) {
        this.$store.commit('beds/setCurrentBedName', value)
      }
    },
    w: {
      get () {
        return this.current.w
      },
      set (value) {
        this.$store.commit('beds/setCurrentBedWidth', value)
      }
    },
    l: {
      get () {
        return this.current.l
      },
      set (value) {
        this.$store.commit('beds/setCurrentBedLength', value)
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
    images: {
      get () {
        return this.current.images
      },
      set (value) {
        this.$store.commit('beds/setCurrentBedImages', value)
      }
    },
    remainingArea () {
      if (!this.location.w) {
        return { l: 99999999999999, w: 99999999999999 }
      }
      let beds = []
      if (this.current.id) {
        beds = clone(this.location.beds).filter(b => {
          if (b.id !== this.current.id) {
            return b
          }
        })
      } else if (this.current.index !== undefined) {
        beds = clone(this.location.beds).filter((b, i) => {
          if (i !== this.current.index) {
            return b
          }
        })
      } else {
        beds = clone(this.location.beds)
      }
      return {
        l: this.location.l - beds.reduce((acc, bed) => acc + bed.l, 0) - this.current.l,
        w: this.location.w - beds.reduce((acc, bed) => acc + bed.w, 0) - this.current.w,
      }
    }
  },
  methods: {
    newBed () {
      this.$store.commit('beds/setCurrentBed', { name: '', description: '', images: [] })
    },
    editBed (bed, i) {
      this.$store.commit('beds/setCurrentBed', { ...clone(bed), index: i })
    },
    deleteBed (i) {
      this.$emit('delete', i)
    },
    done () {
      this.errors = false
      if (!this.name.length) {
        this.errors = 'Name is required'
      }
      if (this.remainingArea.l < 0) {
        this.errors = `Length exceeds remaining length space by ${this.remainingArea.l * -1}cm`
      } else if (this.remainingArea.w < 0) {
        this.errors = `Width exceeds remaining width by ${this.remainingArea.w * -1}cm`
      }
      if (!this.errors) {
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
    color: white;
    outline: none;
    border: 1px solid $grey-500;
    border-radius: 0.25em;
  }
}
</style>