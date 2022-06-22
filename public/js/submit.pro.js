jQuery(function ($) {
  function formatBytes(bytes, decimals = 2) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
  }

  let thumbnail = function (input) {
    if (input.files && input.files[0]) {
      let reader = new FileReader();
      reader.onload = function (e) {
        $('#preview_image').attr('src', e.target.result);
      };
      reader.readAsDataURL(input.files[0]);
    }
  };

  $('#project_image').on('change', function () {
    if ($(this).val() !== '') {
      let imgName = $(this)
        .val()
        .replace(/.*(\/|\\)/, '');
      let exten = imgName.substring(imgName.lastIndexOf('.') + 1);
      let expects = ['jpg', 'jpeg', 'png', 'PNG', 'JPG', 'gif'];

      if (expects.indexOf(exten) == -1) {
        $('#preview_image').attr('src', '');
        alert('Invalid Image!');
        return false;
      }

      if ($(this)[0].files[0].size > ajax_data.max_upload) {
        alert(
          'You can upload maximum ' + formatBytes(ajax_data.max_upload) + '!'
        );
        return false;
      }

      thumbnail(this);
    } else {
      $('#preview_image').attr('src', '');
    }
  });

  // Validate empty value
  let items = [
    'name_of_the_project',
    'project_url',
    'short_description',
    'project_image',
    'project_application',
    'project_grade',
  ];

  items.forEach((element) => {
    $('#' + element + '').on('input change', function () {
      if (element === 'name_of_the_project') {
        let value = $(this)
          .val()
          .substring(0, 23 - 1);
        $(this).val(value);
      }

      if ($(this).val() !== '') {
        $(this).css({ background: '#33384e82', 'border-color': '#ddd' });
      } else {
        $(this).css({
          background: 'rgb(38 5 5 / 25%)',
          'border-color': 'red',
        });
      }
    });
  });

  $('#submit_project').on('click', function (e) {
    items.forEach((element) => {
      if (element !== 'project_image') {
        if ($('#' + element + '').val() === '') {
          e.preventDefault();
          $('#' + element + '').css({
            background: 'rgb(38 5 5 / 25%)',
            'border-color': 'red',
          });
        }
      }
    });
  });
});
