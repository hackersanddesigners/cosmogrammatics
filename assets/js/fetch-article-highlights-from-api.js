const textHighlightMergeUnique = require('./text-highlights-merge-unique')

async function fetchArticleHighlightsFromAPI(article_slug) {
  const url = `/cosmo-api/${ article_slug }`

  return fetch(url)
    .then(response => response.json())
    .then(response => {
      const newData = response
      textHighlightMergeUnique(newData, article_slug)

    })
  .catch(error => {
    return []
  });

}

module.exports = fetchArticleHighlightsFromAPI
