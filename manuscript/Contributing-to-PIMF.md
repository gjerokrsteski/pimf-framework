This tutorial explains the basics of contributing to a project on GitHub via the command-line. The workflow can apply to most projects on GitHub, but in this case, we will be focused on the PIMF project. This tutorial is applicable to OSX, Linux and Windows. This tutorial assumes you have installed Git and you have created a GitHub account. If you haven’t already, look at the PIMF on GitHub documentation in order to familiarize yourself with PIMF’s repositories and branches.

## Forking PIMF
Login to GitHub and visit the PIMF Repository. Click on the Fork button. This will create your own fork of PIMF in your own GitHub account. Your PIMF fork will be located at https://github.com/username/pimf-framework (your GitHub username will be used in place of username).

## Cloning PIMF
Open up the command-line or terminal and make a new directory where you can make development changes to PIMF:

    mkdir pimf-develop
    cd pimf-develop

Next, clone the PIMF repository (not your fork you made):

    git clone https://github.com/gjerokrsteski/pimf-framework.git .

Note: The reason you are cloning the original PIMF repository is – you can always pull down the most recent changes from the PIMF repository to your local repository.

## Adding your Fork
Next, it’s time to add the fork you made as a remote repository:

    git remote add fork git@github.com:username/pimf-framework.git

Remember to replace username** with your GitHub username. *This is case-sensitive. You can verify that your fork was added by typing:

    git remote

Now you have a pristine clone of the PIMF repository along with your fork as a remote repository. You are ready to begin branching for new features or fixing bugs.

## Creating Branches
First, make sure you are working in the develop branch. If you submit changes to the master branch, it is unlikely they will be pulled in anytime in the near future. For more information on this, read the documentation for PIMF on GitHub. To switch to the develop branch:

    git checkout develop

Next, you want to make sure you are up-to-date with the latest PIMF repository. If any new features or bug fixes have been added to the PIMF project since you cloned it, this will ensure that your local repository has all of those changes. This important step is the reason we originally cloned the PIMF repository instead of your own fork.

    git pull origin develop

Now you are ready to create a new branch for your new feature or bug-fix. When you create a new branch, use a self-descriptive naming convention. For example, if you are going to fix a bug in Pimf\Util\Cache, name your branch bug/caching:

    git branch bug/caching
    git checkout bug/caching

Switched to branch 'bug/caching'

Note: Create one new branch for every new feature or bug-fix. This will encourage organization, limit interdependency between new features/fixes and will make it easy for the PIMF team to merge your changes into the PIMF core. Now that you have created your own branch and have switched to it, it’s time to make your changes to the code. Add your new feature or fix that bug.

## Committing
Now that you have finished coding and testing your changes, it’s time to commit them to your local repository. First, add the files that you changed/added:

    git add pimf/bug/caching

Next, commit the changes to the repository:

    git commit -s -m "I added some more stuff to the caching."

* **-s** means that you are signing-off on your commit with your name. This tells the PIMF team knows that you personally agree to your code being added to the PIMF core.
* **-m** is the message that goes with your commit. Provide a brief explanation of what you added or changed.

## Pushing to your Fork
Now that your local repository has your committed changes, it’s time to push your new branch to your fork that is hosted in GitHub:

    git push fork feature/caching

Your branch has been successfully pushed to your fork on GitHub.

## Submitting a Pull Request
The final step is to submit a pull request to the PIMF repository. This means that you are requesting that the PIMF team pull and merge your changes to the PIMF core. In your browser, visit your PIMF fork at https://github.com/username/pimf. Click on [Pull Request]. Next, make sure you choose the proper base and head repositories and branches:

* base repo: pimf/pimf
* base branch: develop
* head repo: username/pimf
* head branch: feature/[name of your feature]

Use the form to write a more detailed description of the changes you made and why you made them. Finally, click [Send pull request]. That’s it! The changes you made have been submitted to the PIMF team.
What’s next?

Do you have another feature you want to add or another bug you need to fix? First, make sure you always base your new branch off of the develop branch:

    git checkout develop

Then, pull down the latest changes from PIMF’s repository:

    git pull origin develop

Now you are ready to create a new branch and start coding again!

