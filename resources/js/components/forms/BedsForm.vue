<template>
  <div>
    <p v-if="errors" class="error">{{ errors }}</p>
    <Form :canDelete="!!current.id" @remove="$emit('remove', current)" @submit="submitForm">
      <template #inputs>
        <Display label="Location" :val="location.name"/>
        <Input v-model="name" label="Name" maxlength="255" :flex="true" placeholder="Herbs Bed" required/>
        <Input v-model="description" type="textarea" label="Description" placeholder="Nice sunny location. Snails seem to hate it here!" :flex="true" maxlength="255"/>
        <Images :modelValue="images" label="Images" multiple @addImage="addImage" @removeImage="removeImage"/>
        <!-- <Input :modelValue="image" type="file" label="Image" :flex="true" @change="e => image = e.target.value"/> -->
      </template>
      <template #buttons>
        <Button type="submit" classes="secondary2" :disabled="loading">Done</Button>
        <slot name="buttons"></slot>
      </template>
    </Form>
  </div>
</template>

<script>
import { clone } from '../../utils/helpers'
import { updateLocation } from '../../utils/api'

export default {
  name: 'BedsForm',
  emits: ['done', 'remove'],
  data () {
    return {
      errors: false,
      loading: false
    }
  },
  computed: {
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
    addImage (image) {
      this.$store.commit('beds/addImageToCurrentBed', { bed_id: this.current.id || 0, name: image })
    },
    removeImage (index) {
      this.images.splice(index, 1)
    },
    submitForm (e) {
      e.preventDefault()
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
        this.loading = true
        let beds = clone(this.location.beds)
        if (this.current.id) {
          beds = beds.map(b => {
            if (b.id === this.current.id) {
              return this.current
            }
            return b
          })
        } else {
          beds.push(this.current)
        }
        updateLocation(this, this.location.id, { ...this.location, beds }).then(() => {
          this.$emit('done', this.current)
          this.$store.commit('beds/setCurrentBed', null)
          this.loading = false
        })
      }
    }
  }
}
</script>

<style scoped lang="scss">
</style>