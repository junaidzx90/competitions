const comp = new Vue({
  el: '#competitions',
  data: {
    isDisabled: false,
    dateFilters: [],
    applicationFilter: '',
    gradeFilter: '',
    ageFilter: '',
    currentDate: '',

    competitions: [],
    competitionsMax: 0,
    currentPage: 1,
  },
  methods: {
    getCategoryValue: function (value) {
      switch (value) {
        case false:
          return '....';
          break;
        default:
          return value;
          break;
      }
    },
    prevDateFilter: function (event) {
      let sliderWrap = jQuery(event.target).parents('.date_filters').find('ul');
      let firstItem = sliderWrap.find('li.fitem_visible').first();
      let lastItem = sliderWrap.find('li.fitem_visible').last();

      if (firstItem.prev().length > 0) {
        firstItem.prev().addClass('fitem_visible');
        lastItem.removeClass('fitem_visible');
      }
    },
    nextDateFilter: function (event) {
      let sliderWrap = jQuery(event.target).parents('.date_filters').find('ul');
      let lastItem = sliderWrap.find('li.fitem_visible').last();
      let firstItem = sliderWrap.find('li.fitem_visible').first();

      if (lastItem.next('li').length > 0) {
        lastItem.next('li').addClass('fitem_visible');
        firstItem.removeClass('fitem_visible');
      }
    },
    dateFilter: function (event, value) {
      jQuery(event.target)
        .parent('li')
        .toggleClass('activeFilter')
        .siblings()
        .removeClass('activeFilter');

      if (jQuery(event.target).parent('li').hasClass('activeFilter')) {
        comp.currentDate = value;
      } else {
        comp.currentDate = '';
      }

      comp.currentPage = 1;
      jQuery.ajax({
        type: 'get',
        url: ajax_data.ajaxurl,
        data: {
          action: 'get_competition_results',
          nonce: ajax_data.nonce,
          page: 1,
          date_filter: comp.currentDate,
          application_filter: comp.applicationFilter,
          grade_filter: comp.gradeFilter,
          age_filter: comp.ageFilter,
        },
        beforeSend: () => {
          comp.isDisabled = true;
        },
        dataType: 'json',
        success: function (response) {
          if (response.competitions) {
            comp.competitions = response.competitions;
          }
          if (response.maxpages) {
            comp.competitionsMax = response.maxpages;
          }
          comp.isDisabled = false;
        },
      });
    },
    categoryFilter: function () {
      comp.currentPage = 1;

      jQuery.ajax({
        type: 'get',
        url: ajax_data.ajaxurl,
        data: {
          action: 'get_competition_results',
          nonce: ajax_data.nonce,
          page: 1,
          date_filter: comp.currentDate,
          application_filter: comp.applicationFilter,
          grade_filter: comp.gradeFilter,
          age_filter: comp.ageFilter,
        },
        beforeSend: () => {
          comp.isDisabled = true;
        },
        dataType: 'json',
        success: function (response) {
          if (response.competitions) {
            comp.competitions = response.competitions;
          }
          if (response.maxpages) {
            comp.competitionsMax = response.maxpages;
          }
          comp.isDisabled = false;
        },
      });
    },
    loadmore_projects: function () {
      jQuery.ajax({
        type: 'get',
        url: ajax_data.ajaxurl,
        data: {
          action: 'get_competition_results',
          nonce: ajax_data.nonce,
          page: comp.currentPage + 1,
          date_filter: comp.currentDate,
          application_filter: comp.applicationFilter,
          grade_filter: comp.gradeFilter,
          age_filter: comp.ageFilter,
        },
        beforeSend: () => {
          comp.isDisabled = true;
        },
        dataType: 'json',
        success: function (response) {
          if (response.competitions) {
            response.competitions.forEach((element) => {
              comp.competitions.push(element);
            });
            comp.currentPage += 1;
          }

          comp.isDisabled = false;
        },
      });
    },
  },
  updated: function () {},
  mounted: function () {
    let competitions = new Promise((resolve, reject) => {
      jQuery.ajax({
        type: 'get',
        url: ajax_data.ajaxurl,
        data: {
          action: 'get_competition_results',
          nonce: ajax_data.nonce,
          page: 1,
        },
        beforeSend: () => {
          jQuery(document).find('.preloader-overlay').css({
            '-webkit-transform': 'translateY(0vh)',
            transform: 'translateY(0vh)',
          });
        },
        dataType: 'json',
        success: function (response) {
          resolve(response);
        },
      });
    });

    competitions.then((response) => {
      if (response.competitions) {
        comp.competitions = response.competitions;
        jQuery(document).find('.preloader-overlay').css({
          '-webkit-transform': 'translateY(100vh)',
          transform: 'translateY(100vh)',
        });
      }
      if (response.dates) {
        comp.dateFilters = response.dates;
      }
      if (response.maxpages) {
        comp.competitionsMax = response.maxpages;
      }
    });

    setTimeout(() => {
      jQuery("#competitions").show();
    }, 300);
  },
});
