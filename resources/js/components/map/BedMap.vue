<template>
  <div
    :id="'bedmap' + bed?.id || 'new'"
    :class="['bed-map', { active: current.id === bed.id }, { selectable: dragging || selectionMode }, { dragging }]"
    parent-id="grid"
    :style="styles"
    :title="`${bed.name}`"
    @mouseup.prevent.self="selectBed"
    @mouseenter="hovering = true"
    @mouseleave="hovering = false">
    <div v-show="hovering" class="resize-btn bottom-right" @mousedown="beginResize"></div>
  </div>
</template>
<script>
import { updateBed } from '../../utils/api'
import { clone, draggable, resizeShape } from '../../utils/helpers'

export default {
  name: 'BedMap',
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
  emits: ['selectBed', 'editingBed'],
  data () {
    return {
      loading: false,
      parent: null,
      dragging: false,
      bedCopy: clone(this.bed),
      hovering: false,
      resizing: false
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
        this.dragging = false
        updateBed(this, this.bed.id, { ...this.bed, x: this.bedCopy.x, y: this.bedCopy.y })
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
    styles () {
      if (!this.parent) {
        return {}
      }
      return {
        top: (this.bedCopy.y / this.parent.clientHeight) * 100 + '%',
        left: (this.bedCopy.x / this.parent.clientWidth) * 100 + '%',
        width: (this.bedCopy.width / this.parent.clientWidth) * 100 + '%',
        height: (this.bedCopy.height / this.parent.clientHeight) * 100 + '%'
      }
    },
    current () {
      return this.$store.state.beds.current || {}
    }
  },
  methods: {
    beginResize () {
      this.resizing
      resizeShape(this.parent, this.$el, this.bed.x, this.bed.y, (shape, width, height) => {
        // moving
        if (this.resizing) {
          this.bedCopy.width = width
          this.bedCopy.height = height
        }
      },(shapeRect) => {
        // end
        this.resizing = false
        updateBed(this, this.bed.id, { ...this.bed, width: shapeRect.width, height: shapeRect.height })
     })
    },
    selectBed () {
      if (!this.dragging && !this.resizing) {
        this.$emit('editingBed')
        this.$store.commit('beds/setCurrentBed', this.bed)
      }
      if (this.selectionMode) {
        this.$emit('editingBed')
        this.$emit('selectBed', this.bed)
      }
    }
  },
}
</script>

<style scoped lang="scss">
.bed-map {
  position: absolute;
  background: $secondary;
  > :not(.resize-btn) {
    pointer-events: none;
    user-select: none;
  }
  &:hover {
    cursor: pointer;
  }
  &.selectable {
    z-index: 9;
    background-color: $primary3;
    border: 2px dashed $textColour;
  }
  &.dragging {
    cursor: grabbing;
  }
  :deep(.shapeWidth),
  :deep(.shapeHeight) {
    position: absolute;
    color: $textColour;
    padding: 0.25em 0.5em;
    font-size: $fsSmall;
  }
  :deep(.shapeWidth) {
    top: -25px;
    left: 50%;
    transform: translateX(-50%);
  }
  :deep(.shapeHeight) {
    left: -45px;
    top: 50%;
    transform: translateY(-50%);
  }
  .resize-btn {
    position: absolute;
    width: 10px;
    height: 10px;
    background-color: white;
    border: 1px solid $textColour;
    z-index: 1001;
    &.top-left {
      top: -5px;
      left: -5px;
      cursor: nwse-resize;
    }
    &.top-right {
      top: -5px;
      right: -5px;
      cursor: nesw-resize;
    }
    &.bottom-left {
      bottom: -5px;
      left: -5px;
      cursor: nesw-resize;
    }
    &.bottom-right {
      bottom: -5px;
      right: -5px;
      cursor: nwse-resize;
    }
  }
}
</style>