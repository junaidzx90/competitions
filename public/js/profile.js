Vue.component('multiselect', window.VueMultiselect.default);
const prof = new Vue({
  el: '#user_dashboard',
  data: {
    isDisabled: false,
    isEditInfo: false,
    avatars: [],
    points: null,
    profileInfoStore: {
      avatar: null,
      name: 'No name',
      userBio: '....',
      school: '....',
      country: '....',
      grade: '',
      age: '',
      multiapplications: [],
      multiGrades: [],
      multiAges: [],
    },
    applications: [],
    grades: [],
    ages: [],
    projectsList: [],
    dateFilters: [],
    applicationFilter: '',
    gradeFilter: '',
    currentDate: '',
    maxProjects: 0,
    currentPage: 1,
  },
  methods: {
    getGrade: function (grade_id) {
      let grade = this.grades.find((el) => el.id === grade_id);
      if (grade && grade.grade !== undefined) {
        return grade.grade;
      }
    },
    getMultiCategories: function (type, Values) {
      switch (type) {
        case 'applications':
          if (Values && Values.length > 0) {
            let obj = Values.map((el) => {
              return el.term_name;
            });
            return obj.join(', ');
          }
          break;
        case 'grades':
          if (Values && Values.length > 0) {
            let obj = Values.map((el) => {
              return el.grade;
            });
            return obj.join(', ');
          }
          break;
        case 'ages':
          if (Values && Values.length > 0) {
            let obj = Values.map((el) => {
              return el;
            });
            return obj.join(', ');
          }
          break;
      }
    },
    avatarSelect: function (avatarId) {
      this.profileInfoStore.avatar = avatarId;
    },
    getProfileImage: function (avatarId) {
      if (null !== avatarId) {
        let field = this.avatars.find((el) => el.id === avatarId);
        if (field !== undefined) {
          return field.url;
        }
      }
    },
    intToString: function (num) {
      num = num.toString().replace(/[^0-9.]/g, '');
      if (num < 1000) {
        return num;
      }
      let si = [
        { v: 1e3, s: 'K' },
        { v: 1e6, s: 'M' },
        { v: 1e9, s: 'B' },
        { v: 1e12, s: 'T' },
        { v: 1e15, s: 'P' },
        { v: 1e18, s: 'E' },
      ];
      let index;
      for (index = si.length - 1; index > 0; index--) {
        if (num >= si[index].v) {
          break;
        }
      }
      return (
        (num / si[index].v)
          .toFixed(2)
          .replace(/\.0+$|(\.[0-9]*[1-9])0+$/, '$1') + si[index].s
      );
    },
    get_points_value: function (points) {
      if (points > 0) {
        return this.intToString(points);
      } else {
        return 0;
      }
    },
    needToEditInfo: function () {
      this.isEditInfo = true;
    },
    saveProfileChanges: function () {
      jQuery.ajax({
        type: 'post',
        url: ajax_data.ajaxurl,
        data: {
          action: 'save_profile_information',
          nonce: ajax_data.nonce,
          data: prof.profileInfoStore,
        },
        beforeSend: function () {
          prof.isDisabled = true;
        },
        dataType: 'json',
        success: function (response) {
          prof.isDisabled = false;
          prof.isEditInfo = false;
        },
      });
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
    dateFilter: function (event, value) {
      jQuery(event.target)
        .parent('li')
        .toggleClass('activeFilter')
        .siblings()
        .removeClass('activeFilter');

      if (jQuery(event.target).parent('li').hasClass('activeFilter')) {
        prof.currentDate = value;
      } else {
        prof.currentDate = '';
      }

      prof.currentPage = 1;
      jQuery.ajax({
        type: 'get',
        url: ajax_data.ajaxurl,
        data: {
          action: 'get_projects_results',
          nonce: ajax_data.nonce,
          page: 1,
          author: ajax_data.userId,
          date_filter: prof.currentDate,
          application_filter: prof.applicationFilter,
          grade_filter: prof.gradeFilter,
        },
        beforeSend: () => {
          prof.isDisabled = true;
        },
        dataType: 'json',
        success: function (response) {
          if (response.projects) {
            prof.projectsList = response.projects;
          }

          if (response.maxpages) {
            prof.maxProjects = response.maxpages;
          }
          prof.isDisabled = false;
        },
      });
    },
    categoryFilter: function () {
      prof.currentPage = 1;

      jQuery.ajax({
        type: 'get',
        url: ajax_data.ajaxurl,
        data: {
          action: 'get_projects_results',
          nonce: ajax_data.nonce,
          page: 1,
          author: ajax_data.userId,
          date_filter: prof.currentDate,
          application_filter: prof.applicationFilter,
          grade_filter: prof.gradeFilter,
        },
        beforeSend: () => {
          prof.isDisabled = true;
        },
        dataType: 'json',
        success: function (response) {
          if (response.projects) {
            prof.projectsList = response.projects;
          }
          if (response.maxpages) {
            prof.maxProjects = response.maxpages;
          }
          prof.isDisabled = false;
        },
      });
    },
    loadmore_projects: function () {
      jQuery.ajax({
        type: 'get',
        url: ajax_data.ajaxurl,
        data: {
          action: 'get_projects_results',
          nonce: ajax_data.nonce,
          page: prof.currentPage + 1,
          author: ajax_data.userId,
          date_filter: prof.currentDate,
          application_filter: prof.applicationFilter,
          grade_filter: prof.gradeFilter,
        },
        beforeSend: () => {
          prof.isDisabled = true;
        },
        dataType: 'json',
        success: function (response) {
          if (response.projects) {
            prof.projectsList = response.projects;
            prof.currentPage += 1;
          }

          if (response.maxpages) {
            prof.maxProjects = response.maxpages;
          }
          prof.isDisabled = false;
        },
      });
    },
    deleteProject: function (id) {
      if (confirm('The project will be deleted permanently.')) {
        jQuery.ajax({
          type: 'post',
          url: ajax_data.ajaxurl,
          data: {
            action: 'delete_project',
            project_id: id,
            nonce: ajax_data.nonce,
          },
          beforeSend: () => {
            prof.isDisabled = true;
          },
          dataType: 'json',
          success: function (response) {
            prof.isDisabled = false;
            if (response.success) {
              prof.projectsList = prof.projectsList.filter(function (el) {
                return el.id !== id;
              });
            }
          },
        });
      }
    },
  },
  updated: function () {},
  mounted: function () {
    let projects = new Promise((resolve, reject) => {
      jQuery.ajax({
        type: 'get',
        url: ajax_data.ajaxurl,
        data: {
          action: 'get_projects_results',
          nonce: ajax_data.nonce,
          author: ajax_data.userId,
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

    projects.then((response) => {
      if (response.projects) {
        prof.projectsList = response.projects;
        jQuery(document).find('.preloader-overlay').css({
          '-webkit-transform': 'translateY(100vh)',
          transform: 'translateY(100vh)',
        });
      }
      if (response.dates) {
        prof.dateFilters = response.dates;
      }
      if (response.maxpages) {
        prof.maxProjects = response.maxpages;
      }
    });

    let profileData = new Promise((resolve, reject) => {
      jQuery.ajax({
        type: 'get',
        url: ajax_data.ajaxurl,
        data: {
          action: 'get_profile_informations',
          author: ajax_data.userId,
          nonce: ajax_data.nonce,
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

    profileData.then((response) => {
      if (response.avatars) {
        prof.avatars = response.avatars;
      }

      if (response.profileData) {
        prof.points = response.points;
        prof.profileInfoStore.avatar = response.profileData.avatar;
        prof.profileInfoStore.name = response.profileData.name;
        prof.profileInfoStore.userBio = response.profileData.userBio;
        prof.profileInfoStore.school = response.profileData.school;
        prof.profileInfoStore.country = response.profileData.country;

        prof.profileInfoStore.multiapplications = response.profileData.multiapplications;

        if(response.profileData.grade && response.profileData.grade !== ""){
          prof.profileInfoStore.grade = parseInt(response.profileData.grade);
        }
        
        if(response.profileData.multiGrades && response.profileData.multiGrades.length > 0){
          prof.profileInfoStore.multiGrades = response.profileData.multiGrades;
        }

        if(response.profileData.age && response.profileData.age !== ""){
          prof.profileInfoStore.age = parseInt(response.profileData.age);
        }
        
        if(response.profileData.multiAges && response.profileData.multiAges.length > 0){
          prof.profileInfoStore.multiAges = response.profileData.multiAges;
        }
       
      }

      if (response.applications) {
        prof.applications = response.applications;
      }
      if (response.grades) {
        prof.grades = response.grades;
      }
      if (response.ages) {
        prof.ages = response.ages;
      }

      jQuery(document).find('.preloader-overlay').css({
        '-webkit-transform': 'translateY(100vh)',
        transform: 'translateY(100vh)',
      });

      setTimeout(() => {
        jQuery("#user_dashboard").show();
      }, 300);
    });
  }
});