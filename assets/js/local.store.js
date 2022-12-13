// <https://github.com/alienzhou/web-highlighter/blob/master/example/local.store.js>

class LocalStore {
    constructor(id) {
        this.key = id !== undefined ? `cosmo-highlight-${id}` : 'cosmo-highlight';
    }

    storeToJson() {
        const store = localStorage.getItem(this.key);
        let sources;
        try {
            sources = JSON.parse(store) || [];
        }
        catch (e) {
            sources = [];
        }
        return sources;
    }

    jsonToStore(stores) {
        localStorage.setItem(this.key, JSON.stringify(stores));
    }

    save(data) {
        const stores = this.storeToJson();
        const map = {};
        stores.forEach((store, idx) => map[store.id] = idx);

        if (!Array.isArray(data)) {
            data = [data];
        }

        data.forEach(store => {
          console.log('store =>', store);
            // update
            if (map[store.id] !== undefined) {
                stores[map[store.id]] = store;
            }
            // append
            else {
                stores.push(store);
            }
        })
        this.jsonToStore(stores);
    }

    forceSave(store) {
        const stores = this.storeToJson();
        stores.push(store);
        this.jsonToStore(stores);
    }

    remove(id) {
        const stores = this.storeToJson();
        let index = null;
        for (let i = 0; i < stores.length; i++) {
            if (stores[i].id === id) {
                index = i;
                break;
            }
        }
        stores.splice(index, 1);
        this.jsonToStore(stores);
    }

    getAll() {
        return this.storeToJson();
    }

    removeAll() {
        this.jsonToStore([]);
    }
}

module.exports = LocalStore
