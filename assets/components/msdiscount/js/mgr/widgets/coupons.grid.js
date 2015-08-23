msDiscount.grid.Coupons = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		id: 'msd-grid-coupons',
		layout: 'anchor',
		url: msDiscount.config.connector_url,
		baseParams: {
			action: 'mgr/coupons/group/getlist'
		},
		fields: ['id', 'name', 'description', 'discount', 'disposable', 'begins', 'ends', 'coupons', 'activated', 'prefix', 'actions'],
		autoHeight: true,
		paging: true,
		remoteSort: true,
		columns: this.getColumns(config),
		sm: new Ext.grid.CheckboxSelectionModel(),
		tbar: this.getTopBar(config),
		listeners: {
			rowDblClick: function(grid, rowIndex, e) {
				var row = grid.store.getAt(rowIndex);
				this.updateCoupons(grid, e, row);
			}
		}
	});
	msDiscount.grid.Coupons.superclass.constructor.call(this,config);
};
Ext.extend(msDiscount.grid.Coupons,MODx.grid.Grid, {

	getMenu: function (grid, rowIndex) {
		var ids = this._getSelectedIds();

		var row = grid.getStore().getAt(rowIndex);
		var menu = msDiscount.utils.getMenu(row.data['actions'], this, ids);

		this.addContextMenuItem(menu);
	},

	onClick: function (e) {
		var elem = e.getTarget();
		if (elem.nodeName == 'BUTTON') {
			var row = this.getSelectionModel().getSelected();
			if (typeof(row) != 'undefined') {
				var action = elem.getAttribute('action');
				if (action == 'showMenu') {
					var ri = this.getStore().find('id', row.id);
					return this._showMenu(this, ri, e);
				}
				else if (typeof this[action] === 'function') {
					this.menu.record = row.data;
					return this[action](this, e);
				}
			}
		}
		return this.processEvent('click', e);
	},

	getTopBar: function() {
		return [{
			text: (MODx.modx23
				? '<i class="icon icon-plus"></i> '
				: '<i class="fa fa-plus"></i> ')
			+ _('msd_btn_coupon_create'),
			handler: this.createCoupons,
			scope: this
		}];
	},

	getColumns: function(config) {
		var columns = {
			id: {hidden: true, width: 50},
			name: {width: 75},
            description: {hidden: true},
			discount: {width: 50},
            disposable: {hidden: true},
			coupons: {width: 50},
			begins: {width: 50, renderer: miniShop2.utils.formatDate},
			ends: {width: 50, renderer: miniShop2.utils.formatDate},
			activated: {width: 50},
			actions: {width: 50, renderer: msDiscount.utils.renderActions, sortable: false, id: 'actions', header: _('msd_actions')}
		};
		var tmp = [];
		for (var i in columns) {
			if (columns.hasOwnProperty(i)) {
				Ext.applyIf( columns[i], {
					header: _('msd_coupons_' +  i),
					dataIndex: i,
					sortable: true,
				});
				tmp.push(columns[i]);
			}
		}
		return tmp;
	},

	createCoupons: function(btn, e) {
		var id = 'msd-window-coupons-create';
		var w = Ext.getCmp(id);
		if (w) {return;}

		w = MODx.load({
			id: id,
			xtype: 'msd-window-coupons',
			record: {id: 0},
			mode: 'create',
			listeners: {
				success: {fn: function(response) {
					this.refresh();
				}, scope: this},
				hide: function(item) {
					window.setTimeout(function() {
						item.close();
					}, 100);
				}
			}
		});
		w.fp.getForm().reset();
		w.show(e.target);
	},

	updateCoupons: function(btn, e, row) {
		if (typeof(row) != 'undefined') {this.menu.record = row.data;}

		var w = MODx.load({
			xtype: 'msd-window-coupons',
			record: this.menu.record,
			mode: 'update',
			listeners: {
				success: {
					fn: function(response) {
						this.refresh();
					}, scope: this
				}
			}
		});
		w.fp.getForm().setValues(this.menu.record);
		w.show(e.target);
	},

	downloadCoupons: function() {
		var ids = Ext.util.JSON.encode(this._getSelectedIds());
		MODx.Ajax.request({
			url: msDiscount.config.connector_url,
			params: {
				action: 'mgr/coupons/group/download',
				check: true,
				ids: ids,
			},
			listeners: {
				success:{
					fn:function() {
						location.href = msDiscount.config.connector_url +
						'?action=mgr/coupons/group/download&HTTP_MODAUTH=' +
						MODx.siteId + '&ids=' + ids;
						}, scope:this
				},
				failure: {
					fn: function (response) {
						MODx.msg.alert(_('error'), response.message);
					}, scope: this
				},
			}
		});


	},

	couponsAction: function(method) {
		var ids = this._getSelectedIds();
		if (!ids.length) {
			return false;
		}
		MODx.Ajax.request({
			url: msDiscount.config.connector_url,
			params: {
				action: 'mgr/coupons/group/multiple',
				method: method,
				ids: Ext.util.JSON.encode(ids),
			},
			listeners: {
				success: {
					fn: function () {
						this.refresh();
					}, scope: this
				},
				failure: {
					fn: function (response) {
						MODx.msg.alert(_('error'), response.message);
					}, scope: this
				},
			}
		})
	},

	removeCoupons: function(btn,e) {
		Ext.MessageBox.confirm(
			_('msd_action_remove'),
			_('msd_action_remove_confirm'),
			function(val) {
				if (val == 'yes') {
					this.couponsAction('remove');
				}
			},
			this
		);
	},

	_getSelectedIds: function() {
		var ids = [];
		var selected = this.getSelectionModel().getSelections();

		for (var i in selected) {
			if (!selected.hasOwnProperty(i)) {
				continue;
			}
			ids.push(selected[i]['id']);
		}

		return ids;
	},

});
Ext.reg('msd-grid-coupons-groups', msDiscount.grid.Coupons);


msDiscount.window.Coupons = function(config) {
	config = config || {};
	if (!config.id) {
		config.id = Ext.id();
	}

	Ext.applyIf(config, {
		title: _('msd_coupons_' + (config.mode || 'create')),
		autoHeight: true,
		width: 650,
		url: msDiscount.config.connector_url,
		action: 'mgr/coupons/group/' + (config.mode || 'create'),
		fields: this.getTabs(config),
		keys: this.getKeys(config),
	});
	msDiscount.window.Coupons.superclass.constructor.call(this,config);
};

Ext.extend(msDiscount.window.Coupons,MODx.Window, {

	getTabs: function(config) {
		return [{
			id: config.id + '-tabs',
			xtype: 'modx-tabs',
			bodyStyle: MODx.modx23 ? '' : 'padding: 5px;',
			defaults: { border: false, autoHeight: true },
			border: true,
			activeTab: 0,
			autoHeight: true,
			stateful: false,
			stateId: 'msd-window-coupons-' + config['mode'],
			stateEvents: ['tabchange'],
			getState:function() {return {activeTab: this.items.indexOf(this.getActiveTab())};},
			items: [{
				title: _('msd_sales_main'),
				layout: 'form',
				items: this.getMainFields(config)
			}, {
				title: _('msd_coupons_coupons'),
				disabled: config['mode'] == 'create',
				layout: 'anchor',
				items: {
					xtype: 'msd-grid-coupons',
					record: config.record,
					group_id: config.record.id
				}
			},{
                title: _('msd_coupons_group_users'),
                disabled: config['mode'] == 'create',
                layout: 'anchor',
                items: {
                    xtype: 'msd-grid-coupons-group',
                    record: config.record,
                    type: 'users',
                    coupons_id: config.record.id
                }
            },{
                title: _('msd_coupons_group_products'),
                disabled: config['mode'] == 'create',
                layout: 'anchor',
                items: {
                    xtype: 'msd-grid-coupons-group',
                    record: config.record,
                    type: 'products',
                    coupons_id: config.record.id
                }
            }]
		}];
	},

	getMainFields: function(config) {
		return [{
			xtype: 'hidden', name: 'id'
		}, {
			layout: 'column',
			border: false,
			anchor: '100%',
			style: {margin: '10px 0 0 0'},
			items: [{
				columnWidth: .5,
				layout: 'form',
				defaults: {msgTarget: 'under'},
				items: [
					{xtype: 'textfield', name: 'name', fieldLabel: _('msd_coupons_name'), anchor: '99%'},
				]
			}, {
				columnWidth: .3,
				layout: 'form',
				style: {margin: 0},
				defaults: {msgTarget: 'under'},
				items: [
					{xtype: 'textfield', name: 'prefix', fieldLabel: _('msd_coupons_prefix'), anchor: '99%', disabled: config.mode == 'update', maxLength: msDiscount.config.prefix_length},
				]
			}, {
				columnWidth: .2,
				layout: 'form',
				style: {margin: '10px 0 0 5px'},
				defaults: {msgTarget: 'under'},
				items: [
                    {xtype: 'xcheckbox', fieldLabel: '', boxLabel: _('msd_coupons_disposable'), anchor: '80%' , description: _('msd_coupons_disposable_desc'), name: 'disposable', disabled: config.mode == 'update'}
				]
			}]
		}, {
			layout: 'column',
			border: false,
			anchor: '100%',
			style: {margin: '10px 0 0 0'},
			items: [{
				columnWidth: .5,
				layout: 'form',
				defaults: {msgTarget: 'under'},
				items: [
					{xtype: 'textfield', name: 'discount', fieldLabel: _('msd_coupons_discount'), anchor: '80%'},
					{xtype: 'minishop2-xdatetime', name: 'begins', fieldLabel: _('msd_coupons_begins'), anchor: '99%'},
				]
			}, {
				columnWidth: .5,
				layout: 'form',
				style: {margin: 0},
				defaults: {msgTarget: 'under'},
				items: [
					{xtype: 'numberfield', name: 'coupons', fieldLabel: _('msd_coupons_number'), anchor: '80%'},
					{xtype: 'minishop2-xdatetime', name: 'ends', fieldLabel: _('msd_coupons_ends'), anchor: '99%'},
				]
			}]
		},
        {
            xtype: 'textarea', name: 'description', fieldLabel: _('msd_coupons_description'), anchor: '100%', height: 75}
        ,{
			xtype: 'displayfield', cls: 'panel-desc', html: _('msd_coupons_form_desc'), anchor: '100%'
		}];
	},

	getKeys: function() {
		return [{
				key: Ext.EventObject.ENTER,
				shift: true,
				fn: function() {
					this.submit()
				}, scope: this
		}];
	},

	loadDropZones: function() {},

});
Ext.reg('msd-window-coupons', msDiscount.window.Coupons);


msDiscount.grid.Coupons = function(config) {
	config = config || {};
	if (!config.id) {
		config.id = Ext.id();
	}
	Ext.applyIf(config, {
		layout: 'anchor',
		id: config.id,
		url: msDiscount.config.connector_url,
		baseParams: {
			action: 'mgr/coupons/getlist',
			type: config.type,
			group_id: config.record.id
		},
		fields: ['id', 'group_id', 'code', 'createdon', 'activatedon', 'active', 'order_id', 'num', 'actions'],
		autoHeight: true,
		paging: true,
		remoteSort: true,
		pageSize: 5,
		sm: new Ext.grid.CheckboxSelectionModel(),
		tbar: this.getTopBar(config),
		columns: this.getColumns(config),
	});
	msDiscount.grid.Coupons.superclass.constructor.call(this, config);
};
Ext.extend(msDiscount.grid.Coupons, MODx.grid.Grid, {

	getMenu: function (grid, rowIndex) {
		var row = grid.getStore().getAt(rowIndex);
		var menu = msDiscount.utils.getMenu(row.data['actions'], this);

		this.addContextMenuItem(menu);
	},

	onClick: function (e) {
		var elem = e.getTarget();
		if (elem.nodeName == 'BUTTON') {
			var row = this.getSelectionModel().getSelected();
			if (typeof(row) != 'undefined') {
				var action = elem.getAttribute('action');
				if (action == 'showMenu') {
					var ri = this.getStore().find('id', row.id);
					return this._showMenu(this, ri, e);
				}
				else if (typeof this[action] === 'function') {
					this.menu.record = row.data;
					return this[action](this, e);
				}
			}
		}
		return this.processEvent('click', e);
	},

	getTopBar: function(config) {
		return [];
	},

	getColumns: function() {
		var columns = {
			id: {hidden: true, width: 35},
			code: {width: 75},
			createdon: {width: 50, renderer: miniShop2.utils.formatDate},
			active: {width: 35, renderer: msDiscount.utils.renderBoolean},
			activatedon: {width: 50, renderer: miniShop2.utils.formatDate},
			order_id: {width: 35, renderer: this._renderOrder, header: _('msd_coupons_order')},
			//actions: {width: 35, renderer: msDiscount.utils.renderActions, sortable: false, id: 'actions', header: _('msd_actions')}
		};
		var tmp = [];
		for (var i in columns) {
			if (columns.hasOwnProperty(i)) {
				Ext.applyIf( columns[i], {
					header: _('msd_coupons_' +  i),
					dataIndex: i,
					sortable: true,
				});
				tmp.push(columns[i]);
			}
		}
		return tmp;
	},

	couponAction: function(method) {
		var ids = this._getSelectedIds();
		if (!ids.length) {
			return false;
		}
		MODx.Ajax.request({
			url: msDiscount.config.connector_url,
			params: {
				action: 'mgr/coupons/multiple',
				method: method,
				ids: Ext.util.JSON.encode(ids),
			},
			listeners: {
				success: {
					fn: function() {
						this.refresh();
					}, scope: this
				},
				failure: {
					fn: function (response) {
						MODx.msg.alert(_('error'), response.message);
					}, scope: this
				},
			}
		})
	},

	removeCoupon: function() {
		this.couponAction('remove');
	},

	_getSelectedIds: function() {
		var ids = [];
		var selected = this.getSelectionModel().getSelections();

		for (var i in selected) {
			if (!selected.hasOwnProperty(i)) {
				continue;
			}
			ids.push(selected[i]['id']);
		}

		return ids;
	},

	_renderOrder: function(value, cell, row) {
		if (!value) {
			return '';
		}
		var num = row['data']['num'];
		var ordersPage = MODx.config.manager_url + '?a=' + MODx.action['minishop2:controllers/mgr/orders'];

		return '<a href="' + ordersPage + '&order=' + value + '" target="_blank">#' + num + '</a>';
	},

});
Ext.reg('msd-grid-coupons', msDiscount.grid.Coupons);

msDiscount.grid.CouponsMemberGroup = function(config) {
    config = config || {};
    if (!config.id) {
        config.id = Ext.id();
    }
    Ext.applyIf(config, {
        layout: 'anchor',
        id: config.id,
        url: msDiscount.config.connector_url,
        baseParams: {
            action: 'mgr/coupons/members/getlist',
            type: config.type,
            coupons_id: config.record.id
        },
        fields: ['coupons_id', 'group_id', 'type', 'relation', 'name', 'discount', 'actions'],
        autoHeight: true,
        paging: true,
        remoteSort: true,
        pageSize: 5,
        sm: new Ext.grid.CheckboxSelectionModel(),
        tbar: this.getTopBar(config),
        columns: this.getColumns(config),
    });
    msDiscount.grid.CouponsMemberGroup.superclass.constructor.call(this, config);
};
Ext.extend(msDiscount.grid.CouponsMemberGroup, MODx.grid.Grid, {

    getMenu: function (grid, rowIndex) {
        var row = grid.getStore().getAt(rowIndex);
        var menu = msDiscount.utils.getMenu(row.data['actions'], this);

        this.addContextMenuItem(menu);
    },

    onClick: function (e) {
        var elem = e.getTarget();
        if (elem.nodeName == 'BUTTON') {
            var row = this.getSelectionModel().getSelected();
            if (typeof(row) != 'undefined') {
                var action = elem.getAttribute('action');
                if (action == 'showMenu') {
                    var ri = this.getStore().find('id', row.id);
                    return this._showMenu(this, ri, e);
                }
                else if (typeof this[action] === 'function') {
                    this.menu.record = row.data;
                    return this[action](this, e);
                }
            }
        }
        return this.processEvent('click', e);
    },

    getTopBar: function(config) {
        return [{
            xtype: 'msd-combo-group',
            id: config.id + '-combo',
            type: config.type,
            coupons_id: config.record.id,
            listeners: {
                select: {fn: this.addMember, scope: this}
            }
        }];
    },

    getColumns: function() {
        var columns = {
            name: {width: 75},
            relation: {width: 50, header: _('msd_members_relation'), renderer: this._renderRelation},
            discount: {width: 50},
            actions: {width: 50, renderer: msDiscount.utils.renderActions, sortable: false, id: 'actions', header: _('msd_actions')}
        };
        var tmp = [];
        for (var i in columns) {
            if (columns.hasOwnProperty(i)) {
                Ext.applyIf( columns[i], {
                    header: _('msd_group_' +  i),
                    dataIndex: i,
                    sortable: true,
                });
                tmp.push(columns[i]);
            }
        }
        return tmp;
    },

    addMember: function(combo, row) {
        combo.reset();

        MODx.Ajax.request({
            url: this.config.url,
            params: {
                action: 'mgr/coupons/members/create',
                coupons_id: this.config.record.id,
                group_id: row.id,
                type: this.config.type
            },
            listeners: {
                success: {
                    fn: function(r) {
                        combo.getStore().reload();
                        this.refresh();
                    }, scope: this
                }
            }
        });
    },

    memberAction: function(method) {
        var ids = this._getSelectedIds();
        if (!ids.length) {
            return false;
        }
        MODx.Ajax.request({
            url: msDiscount.config.connector_url,
            params: {
                action: 'mgr/coupons/members/multiple',
                method: method,
                ids: Ext.util.JSON.encode(ids),
            },
            listeners: {
                success: {
                    fn: function() {
                        this.refresh();
                        Ext.getCmp(this.config.id + '-combo').getStore().reload();
                    }, scope: this
                },
                failure: {
                    fn: function (response) {
                        MODx.msg.alert(_('error'), response.message);
                    }, scope: this
                },
            }
        })
    },

    switchMember: function() {
        this.memberAction('switch');
    },

    removeMember: function() {
        this.memberAction('remove');
    },

    _renderRelation: function(value) {
        if (value == 'in') {
            return '<span style="color:green;">' + _('msd_members_relation_in') + '</span>';
        }
        else if (value == 'out') {
            return '<span style="color:red;">' + _('msd_members_relation_out') + '</span>';
        }
        else {
            return value;
        }
    },

    _getSelectedIds: function() {
        var ids = [];
        var selected = this.getSelectionModel().getSelections();

        for (var i in selected) {
            if (!selected.hasOwnProperty(i)) {
                continue;
            }
            ids.push({
                coupons_id: selected[i]['data']['coupons_id'],
                group_id: selected[i]['data']['group_id'],
                type: selected[i]['data']['type'],
            });
        }

        return ids;
    },

});
Ext.reg('msd-grid-coupons-group', msDiscount.grid.CouponsMemberGroup);