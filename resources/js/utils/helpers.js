
export const clone = (obj) => {
  return JSON.parse(JSON.stringify(obj))
}

export const isEmpty = (obj) => {
  return Object.keys(obj).length === 0
}

export const isOverlapping = (el1, el2) => {
  const rect1 = el1.getBoundingClientRect()
  const rect2 = el2.getBoundingClientRect()
  return !(
    rect1.right < rect2.left ||
    rect1.left > rect2.right ||
    rect1.bottom < rect2.top ||
    rect1.top > rect2.bottom
  )
}
export const arrangePlantsInBedWithOverlapCheck = (lastEntry, bed) => {
  const res = []
  const bedEl = document.createElement('div')
  bedEl.classList.add('bed-container-poscheck')
  bedEl.style.width = `${bed.l}px`
  bedEl.style.height = `${bed.w}px`
  bedEl.style.position = 'relative'
  bedEl.style.backgroundColor = 'black'
  document.body.appendChild(bedEl)

  let x = 0
  let y = 0
  const step = 5 // Increment step for x and y when moving to find space

  for (let i = 0; i < lastEntry.qty; i++) {
    const plantEl = document.createElement('div')
    plantEl.classList.add('bed-plant-poscheck')
    plantEl.style.padding = `${lastEntry.area / 2}px`
    plantEl.style.position = 'absolute'
    plantEl.style.backgroundColor = 'green'

    // Find a position that does not overlap
    let placed = false
    while (!placed) {
      plantEl.style.left = `${x}px`
      plantEl.style.top = `${y}px`
      bedEl.appendChild(plantEl) // Temporarily append to check overlap

      const isOverlappingAny = Array.from(bedEl.children).some((existingPlant) => {
        return existingPlant !== plantEl && isOverlapping(plantEl, existingPlant)
      })

      if (isOverlappingAny) {
        bedEl.removeChild(plantEl) // Remove to retry placement
        x += step
        if (x + lastEntry.area >= bed.l) {
          x = 0
          y += step
        }
        if (y + (lastEntry.area / 2) > bed.w) {
          console.warn("Not enough space to place all plants.")
          return res // Stop placement if no space is left
        }
      } else {
        placed = true
      }
    }

    res.push({ x, y })
  }

  return res
}

export const draggable = (el, relativeEl, start, update, end, padding = 0) => {
  let isDragging = false
  let offsetX = 0
  let offsetY = 0

  const onMouseMove = (e) => {
    if (!isDragging) {
      return
    }

    const parentRect = relativeEl.getBoundingClientRect()
    let x = e.clientX - parentRect.left - offsetX
    let y = e.clientY - parentRect.top - offsetY

    const maxX = relativeEl.offsetWidth - el.offsetWidth
    const maxY = relativeEl.offsetHeight - el.offsetHeight

    x = Math.max(0, Math.min(maxX, x))
    y = Math.max(0, Math.min(maxY, y))

    update({ x, y })
  }

  const onMouseUp = (e) => {
    if (!isDragging) {
      return
    }

    isDragging = false

    const parentRect = relativeEl.getBoundingClientRect()
    const x = e.clientX - parentRect.left - offsetX
    const y = e.clientY - parentRect.top - offsetY

    document.removeEventListener('mousemove', onMouseMove)
    document.removeEventListener('mouseup', onMouseUp)

    const finalX = Math.max(0, Math.min(relativeEl.offsetWidth - el.offsetWidth, x))
    const finalY = Math.max(0, Math.min(relativeEl.offsetHeight - el.offsetHeight, y))

    end({ x: finalX, y: finalY })
  }

  el.addEventListener('mousedown', (e) => {
    if (e.target !== el) {
      return
    }

    if (padding) {
      const rect = el.getBoundingClientRect()
      if (
        e.clientX < rect.left + padding ||
        e.clientX > rect.right - padding ||
        e.clientY < rect.top + padding ||
        e.clientY > rect.bottom - padding
      ) {
        return
      }
    }

    const elRect = el.getBoundingClientRect()
    const parentRect = relativeEl.getBoundingClientRect()

    offsetX = e.clientX - elRect.left
    offsetY = e.clientY - elRect.top

    const startX = elRect.left - parentRect.left
    const startY = elRect.top - parentRect.top

    isDragging = true

    // start({ x: startX, y: startY })

    document.addEventListener('mousemove', onMouseMove)
    document.addEventListener('mouseup', onMouseUp)
  })
}

