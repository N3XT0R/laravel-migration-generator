# docs/conf.py

project = 'Laravel Migration Generator'
author = 'N3XT0R'
release = '8.0.0'

version = release

extensions = [
    'myst_parser',
]

source_suffix = {
    '.md': 'markdown',
}

master_doc = 'index'
exclude_patterns = []
html_theme = 'furo'

import os
if os.environ.get('READTHEDOCS') == 'True':
    github_user = 'N3XT0R'
    github_repo = 'laravel-migration-generator'
    github_version = 'master'

    html_context = {
        'display_github': True,
        'github_user': github_user,
        'github_repo': github_repo,
        'github_version': github_version,
        'conf_py_path': '/docs/',
    }