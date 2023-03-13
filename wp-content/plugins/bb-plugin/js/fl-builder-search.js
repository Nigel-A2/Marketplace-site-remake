(function($) {

    var Search = {

        /**
         * Kick-off the query
         *
         * @since 2.0
         * @param {Object} query The query
         * @return {Object} The response
         */
        query: function( query ) {

            var data    = {},
            	kind    = null,
            	results = {
	                library: {}
	            },
	            foundItems = null;

            query = this.normalizeQuery(query);

            // Reduce data by kind
            if ( !_.isNull( query.kind ) ) {
                for( var i in query.kind ) {
                    kind = query.kind[ i ];
                    data[ kind ] = FLBuilderConfig.contentItems[ kind ];
                }
            } else {
                data = FLBuilderConfig.contentItems;
            }

            foundItems = this.findMatches( query, data );
            results.library = this.formatResults( foundItems, query );

            return results;
        },

        /**
         * Make sure the query object is well-formed.
         *
         * @since 2.0
         * @param {Object} query The query
         * @return {Object} The query
         */
        normalizeQuery: function( query ) {
            var defaultQuery = {
				kind: null,
                type: null,
				category: null,
                group: null,
                enabled: true, /* pertains only to modules */
                global: null, /* pertains only to user row and module templates */
                searchTerm: null,
				categorized: false,
			};

			query = _.extend(defaultQuery, query);

            if ( _.isString( query.kind ) ) {
                query.kind = [ query.kind ];
            }

            return query;
        },

        /**
         * Find the objects in the dataset that match query criteria.
         *
         * @since 2.0
         * @param {Object} query
         * @param {Object} data
         * @return {Object}
         */
        findMatches: function( query, data ) {
            var foundItems = {},
            	typeName   = null,
            	objects    = null,
            	object     = null,
            	inArray    = null,
            	matches    = null,
            	i          = null;

			for ( typeName in data ) {

				objects = data[ typeName ];

				foundItems[ typeName ] = {
					items: []
				};

				for ( i in objects ) {

					object = objects[i];

                    // Test for category matches.
                    if ( ! _.isUndefined( query.category ) && ! _.isNull( query.category ) ) {
                        if ( ! this.matchesCategory( object.category, query.category ) ) {
	                        continue;
                        }
                    }

                    switch ( typeName ) {
                        case 'template':

                            // Content type - module | row | layout
                            if ( ! _.isUndefined( query.content ) && ! _.isNull( query.content ) ) {

                                inArray = _.includes( query.content, object.content );
                                matches = query.content === object.content;

                                if ( ! inArray && ! matches) {
	                                continue;
                                }
                            }

                            // Text type matches - core | user
                            if ( ! _.isUndefined( query.type ) && ! _.isNull( query.type ) ) {
                                if ( query.type !== object.type ) {
	                                continue;
                                }
                            }

                            if ( !_.isNull( query.group ) ) {
                                var queryGroup = query.group,
                                    objectGroup = object.group;

                                // Normalize group into arrays
                                if ( _.isString( queryGroup) ) {
                                    queryGroup = [ queryGroup ];
                                }
                                if ( _.isString( objectGroup) ) {
                                    objectGroup = [ objectGroup ];
                                }
                                if ( _.isEmpty( queryGroup ) || _.isEmpty( objectGroup ) ) {
                                    continue;
                                }

                                var hasGroup = false;
                                for( i in queryGroup ) {
                                    var group = queryGroup[i];
                                    if ( _.includes( objectGroup, group ) ) {
                                        hasGroup = true;
                                    }
                                }
                                if ( !hasGroup ) {
                                    continue;
                                }
                            }

                            break;
                        case 'module':

                            if ( ! _.isNull( query.group ) ) {
                                if ( query.group === false && object.group.length > 0 ) {
	                                continue;
                                }
                                if ( query.group !== false && ! _.includes( object.group, query.group ) ) {
                                    continue;
                                }
                            }

                            break;
                    }

                    if ( ! _.isUndefined( query.searchTerm ) && ! _.isNull( query.searchTerm ) ) {
                        if ( ! this.matchesSearchTerm( object, query.searchTerm ) ) {
	                        continue;
                        }
                    }

					foundItems[ typeName ].items.push( object );
				}
			}

            return foundItems;
        },

		/**
         * @since 2.0
         * @param {Object} objectCat
         * @param {Object} queryCats
         * @return {Boolean}
         */
        matchesCategory: function( objectCat, queryCats ) {
	        var queryCat, i, j, cat, key, value;

            if ( objectCat === queryCats ) {
	            return true;
            }

            if ( _.isString( queryCats ) ) {
	            queryCats = [queryCats];
            }

            // Loop over multipe query categories
            for ( i in queryCats ) {
                queryCat = queryCats[ i ];

                if ( _.isString( objectCat ) && objectCat === queryCat ) {
	                return true;
                }

                if ( _.isArray( objectCat ) ) {
                    for ( j in objectCat ) {
                        cat = objectCat[ j ];
                        if ( cat === queryCat ) {
	                        return true;
                        }
                    }
                }

                if ( _.isObject( objectCat ) ) {
                    for ( key in objectCat ) {
                        value = objectCat[ key ];
                        if ( value === queryCat || key === queryCat ) {
	                        return true;
                        }
                    }
                }
            }

            return false;
        },

		/**
         * @since 2.0
         * @param {Object} obj
         * @param {String} term
         * @return {Boolean}
         */
        matchesSearchTerm: function( obj, term ) {
	        var widgetLowercase,
                moduleWordLowercase,
                termLowercase = term.toLowerCase();

            // Match Slug
        	if ( !_.isUndefined( obj.slug ) && obj.slug.toLowerCase().includes( termLowercase ) ) {
        		return true;
        	}

        	// Match Name
        	if ( !_.isUndefined( obj.name ) && obj.name.toLowerCase().includes( termLowercase ) ) {
        		return true;
        	}

        	// Match Category
        	if ( _.isString(obj.category) && obj.category.toLowerCase().includes( termLowercase ) ) {
        		return true;
        	}

        	// Match Description
        	if ( !_.isUndefined( obj.description ) && obj.description.toLowerCase().includes( termLowercase ) ) {
        		return true;
        	}

        	// Match Widget Base ID (slug equivalent)
        	if ( !_.isUndefined( obj.id_base ) && obj.id_base.includes(term)) {
        		return true;
        	}

        	// If term matches "Widget" or "widget"
        	if ( obj.isWidget ) {
                widgetLowercase = "widget";
                if ( widgetLowercase.includes( termLowercase ) ) {
                    return true;
                }
        	}

            // if term matches "Module" or "module"
            if ( !_.isUndefined( obj.editor_export ) ) {
                moduleWordLowercase = "module";
                if ( moduleWordLowercase.includes( termLowercase ) ) {
                    return true;
                }
            }
        	return false;
        },

		/**
         * @since 2.0
         * @param {Object} foundItems
         * @param {Object} query
         * @return {Object}
         */
        formatResults: function(foundItems, query) {

            if (query.categorized) {
                for( type in foundItems) {
                    var items = foundItems[type].items;
                    foundItems[type].categorized = this.groupBy(items, 'category');
                }
            }
            return foundItems;
        },

		/**
         * @since 2.0
         * @param {Object} items
         * @param {String} propertyName
         * @return {Object}
         */
        groupBy: function(items, propertyName) {
            var groups = {}, propertyValue, propertyValues;

            _.forEach(items, function(item, i, list) {

                propertyValue = item[propertyName];

                if ( _.isNull(propertyValue) || _.isUndefined(propertyValue) ) { return; }
                if ( _.isString(propertyValue) && item[propertyValue] === "") { return; }

                // Group item by single category
                if (_.isString(propertyValue)) {
                    groups[propertyValue] = groups[propertyValue] || [];
                    groups[propertyValue].push(item);
                }

                // Group item into each category in an array
                if (_.isArray(propertyValue)) {
                    propertyValues = propertyValue;
                    _.forEach(propertyValues, function(prop, j, list) {
                        groups[prop] = groups[prop] || [];
                        groups[prop].push(item);
                    });
                }

                // Group item into each value of a hash.
                if (_.isObject(propertyValue)) {
                    propertyValues = propertyValue;
                    _.forEach(propertyValues, function(value, key, list) {
                        groups[value] = groups[value] || [];
                        groups[value].push(item);
                    });
                }
            });

            // if any group only contains blank, lose it.
            return groups;
        },

        /**
        * @since 2.0
        * @param String The search term
        * @return {Object} The response
        */
        search: function(term) {
            var query = {
                searchTerm: term
            }
            var raw = this.query(query),
                response = {
                    total: 0,
                    term: term,
                    sections: {}
                };

            if ( !_.isUndefined(raw.library.module.items) ) {
                var categorized = {};
                for(var i in raw.library.module.items) {
                    var item = raw.library.module.items[i],
                        group = item.group[0],
                        cat = item.category,
                        name = item.name;

                    if ( _.isUndefined(categorized[group]) ) {
                        categorized[group] = {};
                    }
                    if ( _.isUndefined(categorized[group][cat]) ) {
                        categorized[group][cat] = [];
                    }
                    categorized[group][cat].push(item);
                }
                response.grouped = categorized;
            }

            for( var i in raw.library) {
                var type = raw.library[i];
                if ( !_.isUndefined(type.items) && type.items.length > 0) {
                    response.sections[i] = {
                        name: FLBuilderStrings.typeLabels[i],
                        handle: i,
                        type: "",
                        items: type.items
                    };
                    response.total += type.items.length;
                }
            }
            return response;
        }
    }

    /**
    * Public Interface
    */
    FLBuilder.Search = {

        /**
         * Find items that match query object
         *
         * @since 2.0
         * @param {Object} query The query
         * @return {Object} The response
         */
        byQuery: function(query) {
            return Search.query(query);
        },

        /**
         * Find items by search term
         *
         * @since 2.0
         * @param String term
         * @return {Object} The response
        */
        byTerm: function(term) {
            var response = Search.search(term);
            return response;
        }
    }

})(jQuery);
