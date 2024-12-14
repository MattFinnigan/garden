<template>
  <div :class="['plant', { 'dragging': dragging }]" :style="styles">
  </div>
</template>

<script>
import { clone, draggable } from '../../utils/helpers'

export default {
  name: 'Plant',
  emits: ['updatePositions'],
  props: {
    plant: {
      type: Object,
      required: true
    },
    loading: {
      type: Boolean,
      default: false
    }
  },
  data () {
    return {
      plantCopy: clone(this.plant),
      parent: null,
      dragging: false
    }
  },
  mounted () {
    this.parent = this.$el?.parentElement
    draggable(this.$el, this.parent, (start) => {
      if (!this.loading) {
        this.dragging = start
      }
    }, (move) => {
      if (this.dragging) {
        this.plantCopy.x = move.x
        this.plantCopy.y = move.y
      }
    }, (end) => {
      if (this.dragging) {
        // check that no plants are overlapping
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
              this.plantCopy.x = this.dragging.x
              this.plantCopy.y = this.dragging.y
              break
            }
          }
        }
        this.dragging = false
        this.$emit('updatePositions', this.plantCopy.entry)
      }
    }, 5)
  },
  computed: {
    styles () {
      if (!this.parent) {
        return {}
      }
      return {
        top: (this.plantCopy.y / this.parent.clientHeight) * 100 + '%',
        left: (this.plantCopy.x / this.parent.clientWidth) * 100 + '%',
        width: this.plantCopy.entry.spacing_x + 'px',
        height: this.plantCopy.entry.spacing_y + 'px',
        // width: (this.plantCopy.l / this.parent.clientWidth) * 100 + '%',
        // height: (this.plantCopy.w / this.parent.clientHeight) * 100 + '%',
        backgroundImage: `url(./images/upload/${this.plantCopy.entry.crop.plant.image})`
      }
    }
  },
  methods: {
  },
}
</script>

<style scoped lang="scss">
.plant {
  position: absolute;
  background: $primary2;
  border: 1px solid $borderColour;
  border-radius: 50%;
  background-position: center;
  background-size: contain;
  background-repeat: no-repeat;
  min-width: 5px;
  min-height: 5px;
  &:hover {
    cursor: grab;
  }
  &.dragging {
    z-index: 100;
    border: 1px dashed;
    cursor: grabbing;
  }
}
</style>