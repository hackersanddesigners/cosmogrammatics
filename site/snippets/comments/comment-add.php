<h3>Add comment</h3>

<script>
    const auth = btoa([
      "<?= $kirby->option('env')['api_user'] ?>", "<?= $kirby->option('env')['api_pass'] ?>"
    ].join(':'))

    let url = 'comments'
    // conver this to local timezone? or at least in the kirby panel
    let ts = new Date().toISOString().split('.')[0]+"Z"

    let body = {
      'slug': `test-${ts}`,
      'title': '',
      'template': 'comment',
      'content': {
        'user': 'ah',
        'timestamp': ts,
        'article_slug': '',
        'block_id': '',
        'text': '',
        'selection_type': '',
        'selection_text': {
          'x': '',
          'y': ''
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

    fetch(`/api/pages/${url}/children`, {
      method: "POST",
      headers: {
        Authorization: `Basic ${auth}`
      },
      body: JSON.stringify(body)
    })
  .then(response => response.json())
  .then(response => {
    const page = response.data;
    console.log('kirby-api page =>', page)
  })
  .catch(error => {
    console.log('err =>', err)
  })
</script>
