function viewMode() {
  const viewOptions = document.querySelector('.view-options')

  const selectedView = viewOptions.querySelector('input:checked')
  const inputs = Array.from(viewOptions.querySelectorAll('input'))

  inputs.map(input => {
    // set view based on currently selected input radio
    // (eg good for page load / refresh)
    if (input.checked) {
      toggleView(input.value) 
    }

    // set view based on clicked input radio 
    input.addEventListener('click', (e) => {
      const target = e.target
      toggleView(target.value) 
    })
  })

}

function toggleView(targetValue) {
  const columns = Array.from(document.querySelectorAll('.content-wrapper .column'))
  const blocks = Array.from(document.querySelectorAll('.content-wrapper .block'))
  const blocksContent = Array.from(document.querySelectorAll('.content-wrapper .block > .contents'))
  const asides = Array.from(document.querySelectorAll('aside'))
  const threads = Array.from(document.querySelectorAll('.thread'))

  // reset custom settings based on specific view
  columns.map(column => {
    column.setAttribute('data-col-width', '1/1')
  })

  blocks.map(block => {
    block.classList.remove('hidden')
    block.style.boxShadow = 'var(--gridshadow)'
    block.style.padding = '0.5rem'
  })

  blocksContent.map(block => {
    block.classList.remove('font-size-micro')
    block.classList.remove('hidden')
  })

  asides.map(aside => {
    aside.style.maxWidth = 'var(--side-width)'
    aside.style.width = 'var(--side-width)'
    aside.style.setProperty('--n', '1')
  })
  threads.map(thread => thread.classList.remove('hidden'))

  switch(targetValue) {
  case '1':
    // display only article-blocks
    threads.map(thread => thread.classList.add('hidden'))
    break;

  case '2':
    // display article-blocks + comments
    break;

  case '3':
    // display comments + article-blocks
    columns.map(column => {
      column.setAttribute('data-col-width', '1/4')
    })

    blocksContent.map(block => {
      block.classList.add('font-size-micro')
    })

    asides.map(aside => {
      aside.style.width = '67vw'
      aside.style.maxWidth = '67vw'
      aside.style.setProperty('--n', '4')
    })

    break;

  case '4':
    // display comments

    columns.map(column => {
      column.setAttribute('data-col-width', '1/4')
    })

    blocks.map(block => {
      block.style.boxShadow = 'none'
      block.style.padding = '0'
      console.log('block =>', block)
    })

    blocksContent.map(block => {
      block.classList.add('font-size-micro')
      block.classList.add('hidden')
    })

    asides.map(aside => {
      aside.style.width = '78vw'
      aside.style.maxWidth = '78vw'
      aside.style.setProperty('--n', '5')
    })

    break;

  }
}

module.exports = viewMode
