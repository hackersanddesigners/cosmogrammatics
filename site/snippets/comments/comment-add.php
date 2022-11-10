<h3>Add comment</h3>

<script>
    const currentURL = window.location.pathname
    const targetURL = currentURL.slice(1).replace('/', '+')
    let url = `${targetURL}+comments`

    // conver this to local timezone? or at least in the kirby panel
    let ts = new Date().toISOString().split('.')[0]+"Z"

    const articleSlug = currentURL.split('/').pop()

    let body = {
      'slug': `test-${ts}`,
      'fullpath': `${currentURL.slice(1)}`,
      'title': '',
      'template': 'comment',
      'content': {
        'user': 'ah',
        'timestamp': ts,
        'article_slug': articleSlug,
        'block_id': '27bc9c7e-659a-4242-b970-03030c09ec41',
        'text': `posting a comment ${ts}`,
        'selection_type': '',
        'selection_text': {
          'x1': '3',
          'y1': '7'
        },
        'selection_image': {
          'x1': '',
          'y1': '',
          'x2': '',
          'y2': ''
        },
        'selection_audio': {
          't1': '',
          't2': ''
        },
        'selection_video': {
          'x1': '',
          'y1': '',
          't1': '',
          'x2': '',
          'y2': '',
          't2': ''
        }
      }
    }

    const csrf = "<?= csrf() ?>"

    fetch(`/api/pages/${url}/children`, {
      method: "POST",
      headers: {
        "X-CSRF": csrf
      },
      body: JSON.stringify(body)
    })
  .then(response => response.json())
  .then(response => {
    const page = response.data;
    console.log('kirby-api page =>', page)
  })
  .catch(err => {
    console.log('err =>', err)
  })
</script>
