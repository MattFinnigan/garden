
<template>
  <aside class="side-menu">
    <nav>
      <div class="month-select">
        <Select v-model.number="month" :options="monthOptionsLong"/>
      </div>
      <div class="buttons-contain">
        <Button icon :class="['icon', { accent: viewing === 'plants' }]" @click="viewing = 'plants'"><Icon colour="black" name="seedling" maskSize="22px" size="1.4em"></Icon>Plants</Button>
        <Button icon :class="['icon', { accent: viewing === 'crops' }]" @click="viewing = 'crops'"><Icon colour="black" name="view" maskSize="22px" size="1.4em"></Icon>Crops</Button>
      </div>
      <!-- List items -->
      <div class="list-items">
        <ListItem
          v-show="viewing === 'plants'"
          v-for="plant in plants"
          :key="'plant' + plant.id"
          :highlight="plant.id === currentPlant?.id"
          @selected="selectPlant(plant)">
          <template #image>
            <img :src="'./images/upload/' + plant.image" alt="Plant icon">
          </template>
          <template #title>
            {{ plant.name }}
          </template>
          <template #description>
            <p><em>{{ plant.variety }}</em></p>
            <p>Sow {{ monthsShort[plant.sow_from] }} - {{ monthsShort[plant.sow_to] }}</p>
            <p>Harvest: {{ plant.days_to_harvest }} Days</p>
          </template>
          <template #actions>
            <Button classes="sm icon transparent" @click.stop="editPlant(plant)"><Icon name="edit" colour="black"/></Button>
            <Button classes="sm icon transparent" @click.stop="deletePlant(plant)"><Icon name="delete" colour="black"/></Button>
          </template>
        </ListItem>
        <ListItem
          v-show="viewing === 'crops'"
          v-for="crop in crops"
          :key="'crop' + crop.id"
          :highlight="crop.id === currentCrop?.id"
          @selected="selectCrop(crop)">
          <template #image>
            <img :src="'./images/upload/' + crop.plant.image" alt="Plant icon">
          </template>
          <template #title>
            {{ crop.plant.name }} #{{ crop.id }}
          </template>
          <template #description>
            <p><em>{{ crop.plant.variety }}</em></p>
            <p>{{ monthsShort[crop.startMonth].toUpperCase() }} - {{ monthsShort[crop.endMonth].toUpperCase() }}</p>
            <p>Qty: {{ crop.qty }}</p>
          </template>
          <template #actions>
            <Button classes="sm icon transparent" @click.prevent="editCrop(crop)"><Icon name="edit" colour="black"/></Button>
            <Button classes="sm icon transparent" @click.prevent="deleteCrop(crop)"><Icon name="delete" colour="black"/></Button>
          </template>
        </ListItem>
        <div class="add-button">
          <Button v-show="viewing === 'plants'" class="sm icon primary" @click="createPlant"><Icon name="plus"/></Button>
        </div>
      </div>
    </nav>
    <!-- Modals -->
    <!-- Plant form Modal -->
    <Modal v-if="editingPlant" @close="cancelPlant">
      <template #header>
        <h5>Create a new Plant </h5>
        <p>Let's add a Plant with it's first Entry</p>
      </template>
      <template #content>
        <PlantForm @done="plantSubmitted" @cancel="cancelPlant">
          <template #buttons>
            <Button class="primary" type="submit" >Done</Button>
          </template>
        </PlantForm>
      </template>
    </Modal>
    <!-- Crop form Modal -->
    <Modal v-if="editingCrop" @close="cancelCrop">
      <template #header>
        <h5>Edit Crop</h5>
        <p>Let's edit the Crop</p>
      </template>
      <template #content>
        <CropForm @done="cropSubmitted">
          <template #buttons>
            <Button class="secondary outline" @click="showCropEntries">See Entries</Button>
            <Button class="primary" type="submit">Done</Button>
          </template>
        </CropForm>
      </template>
    </Modal>
    <!-- Crop entry form Modal -->
    <Modal v-if="editingCropEntry" cancelText="Back" @close="showCropEntries">
      <template #header>
        <h5>Crop Entry</h5>
        <p>Let's add a Crop Entry</p>
      </template>
      <template #content>
        <CropEntryForm @done="(crop) => { cropSubmitted(crop, true) }">
          <template #buttons>
            <Button class="primary" type="submit">Done</Button>
          </template>
        </CropEntryForm>
      </template>
    </Modal>
    <!-- Crop entries list Modal -->
    <Modal v-else-if="cropEntries !== null && !currEntryImages" @close="cancelCropEntries">
      <template #header>
        <h5>Crop "{{ currentCrop.plant.name }} #{{ currentCrop.id }}" Entries</h5>
        <p>Find entries & add new ones</p>
      </template>
      <template #content>
        <div class="top-row">
          <Button classes="primary icon sm" @click="newEntry"><Icon name="plus"></Icon></Button>
        </div>
        <Table
          v-show="cropEntriesMapped.length > 0"
          :headers="cropEntryHeaders"
          :rows="cropEntriesMapped"
          :actions="{ delete: true }"
          @openImageModal="(cropEntry) => { openImageModal(cropEntry) }"
          @delete="(cropEntry) => { deleteCropEntry(cropEntry) }">
        </Table>
        <div v-show="cropEntriesMapped.length === 0" class="empty-state">
          <p class="text-center"><em>Looks like you haven't added any Crop Entries yet.</em></p>
        </div>
      </template>
    </Modal>
    <!-- Delete confirmation Modal -->
    <Modal v-if="deleteConfirm" @close="cancelDelete">
      <template #header>
        <h5>Delete {{ deleteConfirm.name }}</h5>
        <p>Are you sure you want to delete this {{ deleteConfirm.name }}?</p>
      </template>
      <template #content>
        <p>This action cannot be undone.</p>
      </template>
      <template #buttons>
        <Button class="danger outline" @click="() => {
          if (deleteConfirm.name === 'Plant') {
            deletePlant(currentPlant, true)
          } else if (deleteConfirm.name === 'Crop') {
            deleteCrop(currentCrop, true)
          } else {
            deleteCropEntry(deleteConfirm.obj, true)
          }
        }">Yes</Button>
        <Button class="danger" @click="cancelDelete">No</Button>
      </template>
    </Modal>
    <Modal v-if="currEntryImages !== null" cancelText="Back" @close="currEntryImages = null">
      <template #header>
        <h5>Crop Entry Images</h5>
      </template>
      <template #content>
        <div class="image-grid">
          <img v-for="(image, index) in currEntryImages" :key="index" :src="'./images/upload/' + image.name" :alt="image.name">
        </div>
        <div v-if="!currEntryImages.length" class="empty-content">
          <p class="text-center"><em>Looks like you didn't add any images.</em></p>
        </div>
      </template>
    </Modal>
  </aside>
</template>

<script>
import { monthsShort, monthOptionsLong, defaultCropEntry } from '../../utils/consts'
import { fetchPlants, fetchCrops, deleteCrop, deletePlant, createPlant, deleteCropEntry} from '../../utils/api'
import ListItem from '../layout/ListItem.vue'
import PlantForm from '../forms/PlantForm.vue'
import CropForm from '../forms/CropForm.vue'
import CropEntryForm from '../forms/CropEntryForm.vue'

export default {
  components: {
    PlantForm,
    ListItem,
    CropForm,
    CropEntryForm
  },
  data () {
    return {
      monthOptionsLong: monthOptionsLong(),
      monthsShort: monthsShort(),
      viewing: 'plants',
      deleteConfirm: null,
      cropEntryHeaders: [
        { label: 'Date', key: 'datetimestamp' },
        { label: 'Stage', key: 'stage' },
        { label: 'Action', key: 'action' },
        { label: 'Notes', key: 'notes' },
        { label: 'Images', key: 'images', action: 'openImageModal', icon: 'view' }
      ],
      currEntryImages: null
    }
  },
  mounted () {
    this.month = new Date().getMonth() + 1
    fetchPlants(this)
  },
  computed: {
    currentPlant () {
      return this.$store.state.plants.current
    },
    plants () {
      return this.$store.state.plants.list
    },
    editingPlant () {
      return this.viewing === 'plants' && this.plantEditingMode
    },
    plantEditingMode () {
      return this.$store.state.plants.mode === 'edit'
    },
    crops () {
      return this.$store.state.crops.list
    },
    currentCrop () {
      return this.$store.state.crops.current
    },
    editingCrop () {
      return this.viewing === 'crops' && this.cropEditingMode
    },
    cropEditingMode () {
      return this.$store.state.crops.mode === 'edit'
    },
    cropEntries () {
      return this.$store.state.crop_entries.entriesList
    },
    cropEntriesMapped () {
      return this.cropEntries.map(cropEntry => {
        return {
          id: cropEntry.id,
          stage: `${cropEntry.stage}`,
          action: cropEntry.action,
          datetimestamp: new Date(cropEntry.datetimestamp).toLocaleDateString(),
          notes: cropEntry.notes,
          images: cropEntry.images
        }
      })
    },
    editingCropEntry () {
      return this.cropEntries !== null && this.$store.state.crop_entries.current
    },
    month: {
      get () {
        return this.$store.state.maps.month
      },
      set (value) {
        return this.$store.commit('maps/setMapMonth', value)
      }
    }
  },
  watch: {
    month (val) {
      fetchCrops(this, val)
    },
    viewing (val) {
      if (val === 'crops') {
        this.$store.commit('plants/setCurrentPlant', null)
      } else {
        this.$store.commit('crops/setCurrentCrop', null)
      }
    },
    currentCrop (val) {
      if (val) {
        this.viewing = 'crops'
      }
    },
    cropEditingMode (val) {
      if (val) {
        this.viewing = 'crops'
      }
    },
  },
  methods: {
    selectCrop (crop) {
      this.$store.commit('crops/setCurrentCrop', crop)
    },
    cancelCrop () {
      this.$store.commit('crops/setMode', 'view')
    },
    editCrop (crop) {
      this.$store.commit('crops/setCurrentCrop', crop)
      this.$store.commit('crops/setMode', 'edit')
    },
    deleteCrop (crop, confirmed = false) {
      if (confirmed) {
        deleteCrop(this, crop.id).then(() => {
          fetchCrops(this, this.month)
          this.deleteConfirm = null
        })
      } else {
        this.deleteConfirm = {
          name: 'Crop',
          obj: crop
        }
        this.$store.commit('crops/setMode', 'delete')
      }
    },
    cropSubmitted (crop, backToList = false) {
      // refresh crop list
      this.$store.commit('crop_entries/setCurrentCropEntry', null)
      if (!backToList) {
        this.$store.commit('crop_entries/setCropEntries', null)
        this.$store.commit('crops/setCurrentCrop', null)
        this.$store.commit('crops/setCrops', [])
      }
      fetchCrops(this, this.month).then(() => {
        if (!backToList) {
          this.$store.commit('crops/setCurrentCrop', crop)
          this.$store.commit('crops/setMode', 'view')
        } else {
          this.$store.commit('crop_entries/setCropEntries', crop.crop_entries)
        }
      })
    },
    selectPlant (plant) {
      if (plant.id === this.currentPlant?.id) {
        this.$store.commit('plants/setCurrentPlant', null)
      } else {
        this.$store.commit('plants/setCurrentPlant', plant)
      }
    },
    cancelPlant () {
      this.$store.commit('plants/setCurrentPlant', null)
      this.$store.commit('plants/setMode', 'view')
    },
    createPlant () {
      this.$store.commit('plants/setCurrentPlant', null)
      this.$store.commit('plants/setMode', 'edit')
    },
    plantSubmitted (plant) {
      fetchPlants(this).then(() => {
        this.$store.commit('plants/setCurrentPlant', plant)
        this.$store.commit('plants/setMode', 'view')
      })
    },
    editPlant (plant) {
      this.$store.commit('plants/setCurrentPlant', plant)
      this.$store.commit('plants/setMode', 'edit')
    },
    deletePlant (plant, confirmed = false) {
      this.$store.commit('plants/setCurrentPlant', plant)
      if (confirmed) {
        deletePlant(this, plant.id).then(() => {
          fetchPlants(this)
          this.deleteConfirm = null
          this.$store.commit('plants/setMode', 'view')
        })
      } else {
        this.deleteConfirm = {
          name: 'Plant',
          obj: plant
        }
        this.$store.commit('plants/setMode', 'delete')
      }
    },
    showCropEntries () {
      this.cancelCrop()
      this.$store.commit('crop_entries/setCurrentCropEntry', null)
      this.$store.commit('crop_entries/setCropEntries', this.currentCrop.crop_entries)
    },
    cancelCropEntries () {
      this.$store.commit('crop_entries/setCropEntries', null)
      this.$store.commit('crop_entries/setCurrentCropEntry', null)
    },
    deleteCropEntry (entry, confirmed = false) {
      if (confirmed) {
        deleteCropEntry(this, entry.id).then((response) => {
          this.deleteConfirm = null
          this.$store.commit('crop_entries/setCropEntries', response.data.crop.crop_entries)
        })
      } else {
        this.deleteConfirm = {
          name: 'Crop Entry',
          obj: entry
        }
      }
    },
    openImageModal (cropEntry) {
      this.currEntryImages = cropEntry.images
    },
    newEntry () {
      this.$store.commit('crop_entries/setCurrentCropEntry', defaultCropEntry(this.currentCrop))
    },
    cancelDelete () {
      this.deleteConfirm = null
      this.cancelCrop()
      this.cancelPlant()
      this.cancelCropEntries()
    }
  }
}
</script>
<style lang="scss" scoped>
aside {
  transition: all 0.3s;
  max-height: calc(100vh - 55px);
  position: relative;
  overflow-y:scroll;
  nav {
    border: 1px solid $grey-200;
    width: 100%;
    height: 100%;
    transition: all 0.3s;
    .month-select {
      padding: 10px;
    }
    .buttons-contain {
      display: flex;
      border-top-right-radius: 10px;
      border-top-left-radius: 10px;
      button {
        display: flex;
        flex-direction: column;
        align-items: center;
        color: $textColour;
        gap: 2px;
        flex: 1;
        border-radius: 0;
        &:first-child {
          border-top-left-radius: 10px;
        }
        &:last-child {
          border-top-right-radius: 10px;
        }
      }
    }
    .list-items {
      margin-bottom: 10px;
      .add-button {
        display: flex;
        justify-content: flex-end;
        width: 100%;
        position: absolute;
        bottom: 0;
        padding: 10px 0;
        button {
          margin-right: 10px;
        }
      }
    }
  }
  .top-row {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    padding: 10px;
  }
  .image-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    gap: 10px;
    img {
      width: 100%;
      height: auto;
      border-radius: 10px;
      cursor: pointer;
    }
  }
  @include device (desktop, 'all') {
    nav {
      width: 250px;
    }
  }
}
</style>
