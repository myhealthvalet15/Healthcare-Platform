module.exports = function (grunt) {
  var currentdate = new Date();
  var datetime =
    "Last Sync: " +
    currentdate.getDate() +
    "/" +
    (currentdate.getMonth() + 1) +
    "/" +
    currentdate.getFullYear() +
    " @ " +
    currentdate.getHours() +
    ":" +
    currentdate.getMinutes() +
    ":" +
    currentdate.getSeconds();

  grunt.initConfig({
    pkg: grunt.file.readJSON("package.json"),
    obfuscator: {
      options: {
        banner:
          "/*! <%= pkg.name %> - v<%= pkg.version %> - " +
          datetime +
          " */\n/*! <%= pkg.name %> - Developer: M S Praveen Kumar @mspraveenkumar77@gmail.com - https://www.praveenms.live */\n",
        debugProtection: false,
        debugProtectionInterval: true,
        domainLock: ["mhv-admin.hygeiaes.com"],
      },
      js: {
        options: {
          // options for each sub task
        },
        files: [
          {
            expand: true,
            cwd: "assets/js/",
            src: ["**/*.js"],
            dest: "assets/obfuscated-js/",
            flatten: true, // This will remove the directory structure
          },
        ],
      },
    },

    copy: {
      main: {
        files: [
          {
            expand: true,
            flatten: true,
            src: ["assets/obfuscated-js/*.js"],
            dest: "../Admin-project/public/lib/js/page-scripts/",
            filter: "isFile",
          },
          {
            expand: true,
            flatten: true,
            src: [
              "bower_components/jquery/dist/*.js",
              "bower_components/jquery/dist/*.map",
            ],
            dest: "../Admin-project/public/lib/js/jquery/",
            filter: "isFile",
          },
        ],
      },
    },

    watch: {
      scripts: {
        files: ["assets/js/*.js"],
        tasks: ["obfuscator", "copy"],
        options: {
          spawn: false,
        },
      },
    },
  });
  grunt.loadNpmTasks("grunt-contrib-obfuscator");
  grunt.loadNpmTasks("grunt-contrib-watch");
  grunt.loadNpmTasks("grunt-contrib-copy");
  grunt.registerTask("default", ["obfuscator", "copy", "watch"]);
};
