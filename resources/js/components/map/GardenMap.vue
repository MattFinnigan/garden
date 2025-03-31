<template>
  <div class="garden-map">
    <!-- <div class="controls-row">
      <div class="date-select">
        <Button class="icon secondary" @click="removeDays(7)"><Icon name="rewind" size="16px" maskSize="15px"></Icon></Button>
        <Button class="icon secondary" @click="removeDays(1)"><Icon name="play reverse"></Icon></Button>
        <Button class="icon secondary" @click="addDays(1)"><Icon name="play"></Icon></Button>
        <Button class="icon secondary" @click="addDays(7)"><Icon name="rewind reverse" size="16px" maskSize="15px"></Icon></Button>
      </div>
        <Button class="primary outline icon" @click="zoomToFull"><Icon name="zoomin" colour="primary" maskSize="15px" size="14px"></Icon></Button> 
        <Button class="primary outline icon" @click="zoom += 0.1"><Icon name="zoomin" colour="primary" maskSize="15px" size="14px"></Icon></Button>
        <Button class="primary outline icon" @click="zoom -= 0.1"><Icon name="zoomout" colour="primary" maskSize="15px" size="14px"></Icon></Button>
    </div> -->
    <!-- Bed & crop placements -->
    <div
      id="grid" 
      class="grid"
      ref="grid"
      :style="styles"
      @click.prevent.self="createNewCrop">
      <!-- Beds -->
      <div v-if="newBedExplain" class="new-thing-explain" @mousedown="beginNewBed">
        <h4>Click and drag to create a new Bed</h4>
      </div>
      <BedMap v-for="bed in beds" :key="'bed' + bed.id" :zoom="zoom" :bed="bed" ref="bed" :selectionMode="currentCropEntry && !currentBed" @editingBed="editingBed = true"/>
      <div class="new-bed-btn">
        <Button class="primary icon sm" @click="createNewBed"><Icon name="plus"></Icon></Button>
      </div>
      <!-- Crops -->
      <div v-if="newCropExplain" class="new-thing-explain" @mousedown="beginNewCrop">
        <h4>Click and drag to create a new Crop</h4>
      </div>
    </div>
    <!-- <Crops v-else-if="!loading" :embedded="true"></Crops> -->
     <!-- Bed Form -->
    <Modal v-if="currentBed && editingBed" @close="cancelBed()">
      <template #header>
        <h5>{{ bedFormText.heading }}</h5>
        <p>{{ bedFormText.text }}</p>
      </template>
      <template #content>
        <BedsForm @done="bedSubmitted()">
          <template #buttons>
            <Button type="submit" classes="secondary2" @click="bedSubmitted = bedUpdated">Done</Button>
          </template>
        </BedsForm>
      </template>
    </Modal>
    <!-- <Modal v-if="currentCropEntry && currentBed && mode === 'edit'" v-show="!currentPlant" @close="cancelCropEntry">
      <template #header>
        <h5>Create a new Crop {{ currentCropEntry.crop_id ? 'Entry' : '' }}</h5>
        <p v-if="!currentCropEntry.crop_id">You've selected a bed. Now let's add a Crop with it's first Entry</p>
        <p v-else>Add a new Entry to this Crop</p>
      </template>
      <template #content>
        <CropEntryForm @done="cropEntrySubmitted()" @remove="removeCropEntry" @createPlant="">
          <template #buttons>
            <Button v-if="editingBed" type="submit" classes="secondary2" @click="cropEntrySubmitted = createNewCrop">Submit & create another</Button>
            <Button class="primary" type="submit" @click="cropEntrySubmitted = cancelCropEntry">Done</Button>
          </template>
        </CropEntryForm>
      </template>
    </Modal>
    <Modal v-if="currentCrop && mode !== 'edit'" @close="cancelCropEntry(true)">
      <template #header>
        <h5>Crop #{{ currentCrop.id }} - {{ currentCrop.plant.name }}</h5>
        <p>Select & view a crop entry for more details.</p>
      </template>
      <template #content>
        <Crop :embedded="true" @new="createNewCrop(false)" @close="cancelCropEntry(true)"></Crop>
      </template>
    </Modal> -->
  </div>
</template>

<script>
import { fetchCrops, fetchBeds, createCrop, fetchPlants, deleteCropEntry, updateCropEntry } from '../../utils/api'
import { watchScreenSize, createShape, dimensionsToQty } from '../../utils/helpers'
import { defaultCrop, defaultCropEntry, defaultBed } from '../../utils/consts'

import BedMap from './BedMap.vue'
import BedsForm from '../forms/BedsForm.vue'
import Modal from '../common/Modal.vue'
import CropEntryForm from '../forms/CropEntryForm.vue'
import PlantForm from '../forms/PlantForm.vue'
import Crops from '../pages/Crops.vue'

import Crop from '../pages/Crop.vue'

export default {
  name: 'GardenMap',
  components: {
    BedMap,
    BedsForm,
    Modal,
    CropEntryForm,
    PlantForm,
    Crop,
    Crops
  },
  data () {
    return {
      loading: true,
      newBedExplain: false,
      newCropExplain: false,
      zoom: 1,
      bedSubmitted: null,
      cropEntrySubmitted: null,
      render: true,
      zoom: 1,
      editingBed: false
    }
  },
  mounted () {
    this.fetchCrops()
    fetchBeds(this)
    watchScreenSize(() => {
      this.render = false
      this.$nextTick(() => {
        this.render = true
        this.$nextTick(() => {
          this.zoomToFull()
        })
      })
    })
  },
  computed: {
    month: {
      get () {
        return this.$store.state.maps.month
      },
      set (value) {
        return this.$store.commit('maps/setMapMonth', value)
      }
    },
    maps () {
      return this.$store.state.maps.list
    },
    styles () {
      return {
        height: 'calc(100vh - 53px)',
        width: '100%'
      }
    },
    plants () {
      return this.$store.state.plants.list
    },
    currentPlant () {
      return this.$store.state.plants.current
    },
    beds () {
      return this.$store.state.beds.list
    },
    currentBed () {
      return this.$store.state.beds.current
    },
    bedFormText () {
      return {
        heading: this.currentBed.id ? 'Update Bed' : 'New Bed',
        text: this.currentBed.id ? 'Here you can change the Bed\'s details or you can remove it if there aren\'t any active crops.' : 'You’ve placed your new bed. Now just give it a name, & maybe a happy snap'
      }
    },
    currentCropEntry () {
      return this.$store.state.crop_entries.current
    },
    currentCrop () {
      return this.$store.state.crops.current
    },
    mode () {
      return this.$store.state.crops.mode
    }
  },
  methods: {
    zoomToFull () {
      if (this.$el.offsetWidth > this.$refs.grid?.offsetWidth) {
        this.$refs.grid.style.zoom = (this.$el.offsetWidth / this.$refs.grid?.offsetWidth)
        this.zoom = (this.$el.offsetWidth / this.$refs.grid?.offsetWidth)
      }
    },
    // bed stuff
    createNewBed () {
      const newBed = defaultBed()
      const parent = this.$refs.grid

      this.editingBed = true
      this.$store.commit('crops/setSelectCropMode', false)
      this.newBedExplain = true

      createShape(parent, 'newBed', () => {
        // move
      }, (shapeRect, parentRect, zoomFactor) => {
        // mouse up
        // Update the new bed's properties
        newBed.x = (shapeRect.left - parentRect.left) / zoomFactor
        newBed.y = (shapeRect.top - parentRect.top) / zoomFactor
        newBed.width = shapeRect.width / zoomFactor
        newBed.height = shapeRect.height / zoomFactor
        if (newBed.width > 10 && newBed.height > 10) {
          this.$store.commit('beds/setCurrentBed', newBed)
        } else {
          this.cancelBed()
        }
      }, () => {
        // mouse down
        this.newBedExplain = false
      })
    },
    beginNewBed (e) {
      this.newBedExplain = false
      const ev = new MouseEvent('mousedown', {
        bubbles: false,
        cancelable: true,
        view: window,
        clientX: e.clientX,
        clientY: e.clientY
      })
      this.$refs.grid.dispatchEvent(ev)
    },
    bedUpdated () {
      this.cancelBed()
      this.bedSubmitted = null
    },
    cancelBed ()  {
      this.editingBed = false
      if (this.$refs.grid.querySelector('.newBed')) {
        this.$refs.grid.removeChild(this.$refs.grid.querySelector('.newBed'))
        this.newBedExplain = false
      }
      this.$store.commit('beds/setCurrentBed', null)
    },
    // crop stuff
    createNewCrop () {
      if (!this.currentPlant) {
        return
      }
      this.newCropExplain = true
      const newCrop = defaultCrop(this.currentPlant)
      const parent = this.$refs.grid
      createShape(parent, 'newCrop', () => {
        // move
      }, (shapeRect, parentRect, zoomFactor) => {
        // mouse up
        newCrop.x = (shapeRect.left - parentRect.left) / zoomFactor
        newCrop.y = (shapeRect.top - parentRect.top) / zoomFactor
        newCrop.width = shapeRect.width / zoomFactor
        newCrop.height = shapeRect.height / zoomFactor
        newCrop.qty = dimensionsToQty(newCrop.width, newCrop.height, newCrop.spacing)
        if (newCrop.width > 10 && newCrop.height > 10 && newCrop.qty > 0) {
          // createCrop(this, newCrop).then(response => {
          //   this.$store.commit('crops/setCurrentCrop', response.data.crop)
          // })
        } else {
          this.cancelCrop()
        }
      }, () => {
        // mouse down
        this.newCropExplain = false
      }, { image: '/images/upload/' + this.currentPlant.image, spacing: this.currentPlant.spacing })
    },
    beginNewCrop (e) {
      this.newCropExplain = false
      const ev = new MouseEvent('mousedown', {
        bubbles: false,
        cancelable: true,
        view: window,
        clientX: e.clientX,
        clientY: e.clientY
      })
      this.$refs.grid.dispatchEvent(ev)
    },
    cancelCrop () {
      if (this.$refs.grid.querySelector('.newCrop')) {
        this.$refs.grid.removeChild(this.$refs.grid.querySelector('.newCrop'))
        this.newCropExplain = false
      }
      this.$store.commit('crops/setCurrentCrop', null)
    },
    cancelCropEntry (close = false) {
      if (this.editingBed || close) {
        this.$store.commit('crops/setMode', 'edit')
        this.cropEntrySubmitted = null
        this.$store.commit('crops/setCurrentCrop', null)
        this.cancelBed()
      } else {
        this.$store.commit('crops/setMode', 'view')
      }
      this.$store.commit('crop_entries/setCurrentCropEntry', null)
    },
    cancelCropSelect () {
      this.$store.commit('crops/setSelectCropMode', false)
    },
    removeCropEntry () {
      deleteCropEntry(this, this.currentCropEntry.id)
    },
    fetchCrops () {
      this.loading = true
      if (!this.month) {
        this.month = new Date().getMonth() + 1
      }
      fetchCrops(this, this.month).then(response => {
        this.loading = false
        this.$nextTick(() => {
          this.zoomToFull()
          // this.$nextTick(() => {
          //   this.location.beds.forEach((bed, i) => {
          //     arrangePlantsInBedWithOverlapCheck(bed, (entries) => {
          //       entries.forEach(entry => {
          //         updateCropEntry(this, entry.id, entry, false)
          //       })
          //     })
          //   })
          // })
        })
      })
    },
    selectCropMode () {
      this.$store.commit('crops/setSelectCropMode', !this.$store.state.crops.selectCropMode)
    }
  }
}
</script>

<style scoped lang="scss">
.grid {
  position: relative;
  background: $primary2;
  max-width: 100%;
  overflow: auto;
  zoom: 1;
    background-image:
      repeating-linear-gradient(#ccc 0 1px, transparent 1px 100%),
      repeating-linear-gradient(90deg, #ccc 0 1px, transparent 1px 100%);
  :deep(.newShape) {
    border: 2px dashed $textColour;
    position: absolute;
    border-radius: 0.5em;
    .shapeWidth,
    .shapeHeight,
    .shapeQty {
      position: absolute;
      color: $textColour;
      padding: 0.25em 0.5em;
      font-size: $fsSmall;
    }
    .shapeWidth {
      top: -25px;
      left: 50%;
      transform: translateX(-50%);
    }
    .shapeHeight {
      left: -45px;
      top: 50%;
      transform: translateY(-50%);
    }
    .shapeQty {
      right: -50px;
      top: 50%;
      transform: translateY(-50%);
    }
    &.newBed {
      background-color: $primary3;
    }
    &.newCrop {
      background-color: $secondary2;
    }
  }
  .new-thing-explain {
    width: 100%;
    text-align: center;
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
  .new-bed-btn {
    position: fixed;
    bottom: 10px;
    right: 10px;
  }
}
</style>