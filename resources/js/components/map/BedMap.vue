<template>
  <div class="bed-map" :style="styles">
    <Plant v-for="plant in plants" ref="plants" :plant="plant" :loading="loading" @updatePositions="updatePositions"/>
  </div>
</template>
<script>
import Plant from './Plant.vue'
import { updateCropEntry } from '../../utils/api'
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
    }
  },
  data () {
    return {
      loading: false,
      parent: null,
      dragging: false,
      bedCopy: clone(this.bed)
    }
  },
  mounted () {
    this.parent = this.$el?.parentElement
    if (this.bed.id) {
      draggable(this.$el, this.parent, (start) => {
        if (!this.loading) {
          this.dragging = start
        }
      }, (move) => {
        if (this.dragging) {
          this.bedCopy.x = move.x
          this.bedCopy.y = move.y
        }
      }, (end) => {
        if (this.dragging) {
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
                console.log('collision detected')
                this.bedCopy.x = this.dragging.x
                this.bedCopy.y = this.dragging.y
                break
              }
            }
          }
          this.dragging = false
          this.$emit('updatePositions', this.bedCopy)
        }
      }, 5)
    }
  },
  computed: {
    plants () {
      const res = []
      this.bed.crop_entries.forEach(ce => {
        JSON.parse(ce.plant_pos).forEach(plant => {
          res.push({ ...plant, entry: ce })
        })
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
    }
  },
  methods: {
    updatePositions (entry) {
      this.loading = true
      const data = this.$refs.plants.map(plant => {
        return {
          x: plant.plantCopy.x,
          y: plant.plantCopy.y,
        }
      })
      this.loading = false
      console.log(data)
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
}
</style>