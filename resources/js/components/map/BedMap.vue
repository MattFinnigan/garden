<template>
  <div :id="'bedmap' + bed?.id || 'new'" :class="['bed-map', { active: current.id === bed.id }, { selectable: dragging || selectionMode }, { dragging }]" parent-id="grid" :style="styles" @mouseup.prevent.self="selectBed">
    <Plant v-for="plant in plants" ref="plants" :plant="plant" :loading="loading" @updatePositions="updatePositions"/>
  </div>
</template>
<script>
import Plant from './Plant.vue'
import { updateCropEntry, updateLocation } from '../../utils/api'
import { clone, draggable } from '../../utils/helpers';

export default {
  name: 'BedMap',
  components: {
    Plant
  },
  props: {
    bed: {
      type: Object,
      required: true
    },
    selectionMode: {
      type: Boolean,
      default: false
    }
  },
  emits: ['selectBed', 'positionUpdated', 'editingBed'],
  data () {
    return {
      loading: false,
      parent: null,
      dragging: false,
      bedCopy: clone(this.bed),
      colliding: false
    }
  },
  mounted () {
    this.parent = this.$el?.parentElement
    if (this.bed.id) {
      draggable(this.$el, this.parent, (start) => {}, (move) => {
        if (this.selectionMode) {
          return
        }
        if (!this.dragging) {
          this.dragging = move
        } else {
          this.bedCopy.x = move.x
          this.bedCopy.y = move.y
        }
      }, (end) => {
        if (this.dragging) {
          this.colliding = false
          // check that no beds are overlapping
          for (const c in this.parent.children) {
            const child = this.parent.children[c]
            if (child !== this.$el && typeof child === 'object') {
              const rect1 = this.$el.getBoundingClientRect()
              const rect2 = child.getBoundingClientRect()
              if (rect1.x < rect2.x + rect2.width &&
                rect1.x + rect1.width > rect2.x &&
                rect1.y < rect2.y + rect2.height &&
                rect1.y + rect1.height > rect2.y) {
                // collision detected!
                this.colliding = true
                this.bedCopy.x = this.dragging.x
                this.bedCopy.y = this.dragging.y
                break
              }
            }
          }
          this.dragging = false
          if (!this.colliding) {
            this.loading = true
            const beds = this.$store.state.locations.current.beds.map(bed => {
              if (bed.id === this.bed.id) {
                return this.bedCopy
              }
              return bed
            })
            updateLocation(this, this.location.id, { ...this.location, beds }).then(() => {
              this.$emit('positionUpdated')
              this.loading = false
            })
          }
        }
      })
    }
  },
  watch: {
    bed: {
      handler (val) {
        this.bedCopy = clone(val)
      },
      deep: true
    }
  },
  computed: {
    plants () {
      const res = []
      this.bed.crop_entries.forEach(ce => {
        if (JSON.parse(ce.plant_pos)) {
          JSON.parse(ce.plant_pos).forEach(plant => {
            res.push({ ...plant, entry: ce })
          })
        }
      })
      return res
    },
    styles () {
      if (!this.parent) {
        return {}
      }
      return {
        top: (this.bedCopy.y / this.parent.clientHeight) * 100 + '%',
        left: (this.bedCopy.x / this.parent.clientWidth) * 100 + '%',
        width: (this.bedCopy.l / this.parent.clientWidth) * 100 + '%',
        height: (this.bedCopy.w / this.parent.clientHeight) * 100 + '%'
      }
    },
    current () {
      return this.$store.state.beds.current || {}
    },
    location () {
      return this.$store.state.locations.current
    }
  },
  methods: {
    selectBed () {
      if (!this.dragging) {
        this.$emit('editingBed')
        this.$store.commit('beds/setCurrentBed', this.bed)
      }
      if (this.selectionMode) {
        this.$emit('editingBed')
        this.$emit('selectBed', this.bed)
      }
    },
    updatePositions (entry) {
      this.loading = true
      const data = this.$refs.plants.map(plant => {
        return {
          x: plant.plantCopy.x,
          y: plant.plantCopy.y,
        }
      })
      this.loading = false
      updateCropEntry(this, entry.id, { ...entry, plant_pos: JSON.stringify(data) }, false).then(response => {
        this.loading = false
      })
    }
  },
}
</script>

<style scoped lang="scss">
.bed-map {
  position: absolute;
  background: $secondary2;
  &:hover {
    cursor: pointer;
  }
  &.selectable {
    z-index: 1000;
    background-color: $primary3;
    border: 2px dashed $textColour;
  }
  &.dragging {
    cursor: grabbing;
  }
}
</style>