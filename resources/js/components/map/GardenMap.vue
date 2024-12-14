<template>
  <div v-if="!loading" class="garden-map">
    <div v-if="location" class="controls-row">
      <Select v-model.number="location_id" :options="maps.map(l => { return { label: l.name, value: l.id } })" label="Location"/>
      <div class="date-select">
        <Button class="icon secondary" @click="removeDays(7)"><Icon name="rewind" size="16px" maskSize="15px"></Icon></Button>
        <Button class="icon secondary" @click="removeDays(1)"><Icon name="play reverse"></Icon></Button>
        <Input type="date" v-model="currentDate" @change="fetchMaps"/>
        <Button class="icon secondary" @click="addDays(1)"><Icon name="play"></Icon></Button>
        <Button class="icon secondary" @click="addDays(7)"><Icon name="rewind reverse" size="16px" maskSize="15px"></Icon></Button>
      </div>
      <div class="buttons-contain">
        <Button class="primary outline icon">Grid</Button>
        <Button class="primary icon" @click="showNewMenu = !showNewMenu"><Icon name="plus"></Icon></Button>
        <div :class="['dropdown', { 'show': showNewMenu }]">
          <div class="item">Location</div>
          <div class="item" @click="createNewBed">Bed</div>
          <div class="item">Crop</div>
          <div class="item">Crop Entry</div>
        </div>
      </div>
    </div>
    <div class="grid" ref="grid">
      <div v-if="newBedExplain" class="new-bed-explain" @mousedown="beginNewBed">
        <h4>Click and drag to create a new bed</h4>
      </div>
      <BedMap v-for="bed in location.beds" :key="'bed' + bed.id" :bed="bed" ref="bed" @positionUpdated="fetchMaps"/>
    </div>
    <Modal v-if="currentBed" @close="cancelBed(currentBed)">
      <template #header>
        <h5>{{ bedFormText.heading }}</h5>
        <p>{{ bedFormText.text }}</p>
      </template>
      <template #content>
        <BedsForm @done="(bed) => { cancelBed(bed); fetchMaps() }" @remove="(bed) => { deleteBed(bed, true) }">
          <template #buttons>
            <Button class="primary" type="submit" @click="createNewCrop(bed)">Add a Crop</Button>
          </template>
        </BedsForm>
      </template>
    </Modal>
  </div>
</template>

<script>
import { fetchMaps, updateLocation } from '../../utils/api'
import { arrangePlantsInBedWithOverlapCheck } from '../../utils/helpers'

import BedMap from './BedMap.vue'
import BedsForm from '../forms/BedsForm.vue'
import Modal from '../common/Modal.vue'

export default {
  name: 'GardenMap',
  components: {
    BedMap,
    BedsForm,
    Modal
  },
  data () {
    return {
      loading: true,
      date: new Date().toISOString(),
      newBedExplain: false,
      showNewMenu: false
    }
  },
  mounted () {
    this.fetchMaps()
  },
  computed: {
    currentDate () {
      return this.date.split('T')[0]
    },
    maps () {
      return this.$store.state.maps.list
    },
    location () {
      return this.$store.state.locations.current
    },
    locations () {
      return this.$store.state.locations.list
    },
    location_id: {
      get () {
        return this.$store.state.locations.current?.id
      },
      set (value) {
        this.$store.commit('locations/setCurrentLocation', this.maps.find(l => l.id === value))
      }
    },
    plants () {
      return this.$store.state.plants.list
    },
    currentBed () {
      return this.$store.state.beds.current
    },
    bedFormText () {
      return {
        heading: this.currentBed.id ? 'Update Bed' : 'New Bed',
        text: this.currentBed.id ? 'Here you can change the Bed\'s details or you can remove it. This will DELETE all crops associated with it.' : 'You’ve placed your new bed. Now just give it a name, & maybe a happy snap'
      }
    },
    currentCropEntry () {
      return this.$store.state.crop_entries.current
    }
  },
  methods: {
    createNewCrop (bed) {
      this.$nextTick(() => {
        const promises = []
        if (!this.plants.length) {
          promises.push(fetchPlants(this))
        }
        Promise.all(promises).then(() => {
          const e = {
            location_id: this.location_id,
            bed_id: bed.id,
            action: 'Sowed',
            stage: 'Planned',
            qty: 1,
            notes: null,
            images: [],
            area: 1,
            datetimestamp: new Date().toISOString().slice(0, 16),
            units: []
          }
          const c = {
            plant_id: this.plants[0].id,
            crop_entries: []
          }
          this.$store.commit('crops/setCurrentCrop', c)
          this.$store.commit('crop_entries/setCurrentCropEntry', e)
        })
      })
    },
    deleteBed (bed) {
      updateLocation(this, this.location.id, { ...this.location, beds: this.location.beds.filter(b => b.id !== bed.id) }).then(() => {
        this.$store.commit('beds/setCurrentBed', null)
        this.fetchMaps()
      })
    },
    beginNewBed (e) {
      this.newBedExplain = false
      const ev = new MouseEvent('mousedown', e)
      this.$refs.grid.dispatchEvent(ev)
    },
    cancelBed (bed) {
      if (!bed.id) {
        this.$refs.grid.removeChild(this.$refs.grid.querySelector('.newBed'))
        this.newBedExplain = false
      }
      this.$store.commit('beds/setCurrentBed', null)
    },
    createNewBed () {
      this.newBedExplain = true
      this.showNewMenu = false
      const newBed = {
        x: 0,
        y: 0,
        l: 0,
        h: 0,
        name: '',
        description: '',
        images: [],
        crop_entries: []
      }
      let isDragging = false
      let startX = 0
      let startY = 0
      let shape = null
      const parent = this.$refs.grid

      const onMouseMove = (e) => {
        if (!isDragging || !shape) {
          return
        }

        const parentRect = parent.getBoundingClientRect()

        // Calculate the current mouse position relative to the parent
        const currentX = Math.min(
          parentRect.width,
          Math.max(0, e.clientX - parentRect.left)
        )
        const currentY = Math.min(
          parentRect.height,
          Math.max(0, e.clientY - parentRect.top)
        )

        // Calculate width and height of the shape
        const width = Math.abs(currentX - startX)
        const height = Math.abs(currentY - startY)

        // Update shape dimensions and position
        shape.style.left = `${Math.min(startX, currentX)}px`
        shape.style.top = `${Math.min(startY, currentY)}px`
        shape.style.width = `${width}px`
        shape.style.height = `${height}px`
      }

      const onMouseUp = () => {
        if (!isDragging || !shape) {
          return
        }

        isDragging = false

        document.removeEventListener('mousemove', onMouseMove)
        document.removeEventListener('mouseup', onMouseUp)
        parent.removeEventListener('mousedown', onMouseDown)

        const shapeRect = shape.getBoundingClientRect()
        const parentRect = parent.getBoundingClientRect()

        // Update the new bed's properties
        newBed.x = shapeRect.left - parentRect.left
        newBed.y = shapeRect.top - parentRect.top
        newBed.l = shapeRect.width
        newBed.w = shapeRect.height

        // Check for collisions with existing beds
        const beds = this.$refs.bed.map(bed => bed.$el.getBoundingClientRect())
        const hasCollision = beds.some(bedRect => {
          const bedLeft = bedRect.left - parentRect.left
          const bedTop = bedRect.top - parentRect.top
          const bedRight = bedLeft + bedRect.width
          const bedBottom = bedTop + bedRect.height

          const newBedLeft = newBed.x
          const newBedTop = newBed.y
          const newBedRight = newBed.x + newBed.l
          const newBedBottom = newBed.y + newBed.w

          return !(
            newBedRight <= bedLeft || // No overlap on the left
            newBedLeft >= bedRight || // No overlap on the right
            newBedBottom <= bedTop || // No overlap on the top
            newBedTop >= bedBottom    // No overlap on the bottom
          )
        })

        if (hasCollision) {
          // alert('Collision detected! The new bed overlaps with an existing bed.')
          this.cancelNewBed()
        } else {
          this.$store.commit('beds/setCurrentBed', newBed)
        }
        shape = null
      }

      const onMouseDown = (e) => {
        if (e.target !== parent) {
          return
        }
        this.newBedExplain = false
        const parentRect = parent.getBoundingClientRect()

        startX = Math.min(
          parentRect.width,
          Math.max(0, e.clientX - parentRect.left)
        )
        startY = Math.min(
          parentRect.height,
          Math.max(0, e.clientY - parentRect.top)
        )

        // Create the shape element
        shape = document.createElement('div')
        shape.className = 'newBed'
        shape.style.left = `${startX}px`
        shape.style.top = `${startY}px`
        shape.style.width = '0px'
        shape.style.height = '0px'

        parent.appendChild(shape)
        isDragging = true

        document.addEventListener('mousemove', onMouseMove)
        document.addEventListener('mouseup', onMouseUp)
      }

      parent.addEventListener('mousedown', onMouseDown)
    },
    fetchMaps () {
      this.loading = true
      fetchMaps(this, this.currentDate).then(response => {
        this.loading = false
        this.$store.commit('locations/setCurrentLocation', response.data[0])
        // this.location.beds.forEach(bed => {
        //   bed.crop_entries.forEach(ce => {
        //     console.log(JSON.stringify(arrangePlantsInBedWithOverlapCheck(ce, bed)))
        //   })
        // })
      })
    },
    addDays (days) {
      const date = new Date(this.currentDate)
      date.setDate(date.getDate() + days + 1)
      this.date = date.toISOString()
      this.fetchMaps()
    },
    removeDays (days) {
      const date = new Date(this.currentDate)
      date.setDate(date.getDate() - days)
      this.date = date.toISOString()
      this.fetchMaps()
    }
  }
}
</script>

<style scoped lang="scss">
.controls-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.375em;
  > * {
    flex: 0.3;
  }
  .date-select {
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .buttons-contain {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 10px;
    .dropdown {
      border-radius: 0.5em;
      background: $backgroundColour;
      position: absolute;
      top: 130%;
      right: 0;
      text-align: right;
      z-index: -999;
      width: 120px;
      border: 1px solid $borderColour;
      opacity: 0;
      transition: all 0.3s;
      &.show {
        z-index: 999;
        opacity: 1;
      }
      .item {
        background-color: $backgroundColour;
        padding: 6px 12px;
        cursor: pointer;
        border-bottom: 1px solid $borderColour;
        &:hover {
          background: $secondary2;
        }
        &:first-child {
          border-top-left-radius: 0.5em;
          border-top-right-radius: 0.5em;
          border-top: 0;
        }
        &:last-child {
          border-bottom: 0;
          border-bottom-right-radius: 0.5em;
          border-bottom-left-radius: 0.5em;
        }
      }
    }
  }
}
.grid {
  position: relative;
  background: $primary2;
  height: 500px;
  width: 100%;
  :deep(.newBed) {
    border: 2px dashed $textColour;
    background-color: $primary3;
    position: absolute;
    border-radius: 0.5em;
  }
  .new-bed-explain {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: $primary3;
    border-radius: 0.5em;
    border: 2px dashed $textColour;
    z-index: 999;
    h4 {
      color: $backgroundColour;
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
    }
  }
}
</style>