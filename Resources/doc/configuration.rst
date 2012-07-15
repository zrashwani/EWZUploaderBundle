Configuration
=============

Initial Configuration
---------------------
The configuration is as easy as choosing an upload directory and setting
default file permissions (like size and mime types):

.. configuration-block ::

    .. code-block :: yaml

        ewz_uploader:
            load_jquery: false
            media:
                max_size: 5120k
                mime_types: ['audio/wav', 'audio/x-wav', 'audio/wave', 'audio/x-pn-wav']
                dir: %kernel.root_dir%/../media
                folder: uploads

.. note ::

    If you enabled `load_jquery` it will automatically include the library
    directly from Google API.
