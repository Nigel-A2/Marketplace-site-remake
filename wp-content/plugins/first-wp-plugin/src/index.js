import { useSelect, useDispatch } from '@wordpress/data';
import { store as coreDataStore } from '@wordpress/core-data';
import { decodeEntities } from '@wordpress/html-entities';
import { useState, useEffect, render } from '@wordpress/element';
import { Button, TextControl, SearchControl, Spinner, Modal, SnackbarList } from '@wordpress/components';
import { store as noticesStore } from '@wordpress/notices';
/*
    This file gives the plugin the full range of CRUD (Create, Read, Update, Delete) operations for WordPress pages
    inside the WordPress dashboard by adding a new section to it.

    Main file as defined in package.json
*/

// create edit form
export function EditPageForm( { pageId, onCancel, onSaveFinished } ) {
    const { page, lastError, isSaving, hasEdits } = useSelect(
        ( select ) => ( {
            page: select( coreDataStore ).getEditedEntityRecord( 'postType', 'page', pageId ),
            lastError: select( coreDataStore ).getLastEntitySaveError( 'postType', 'page', pageId ),
            isSaving: select( coreDataStore ).isSavingEntityRecord( 'postType', 'page', pageId ),
            hasEdits: select( coreDataStore ).hasEditsForEntityRecord( 'postType', 'page', pageId ),
        } ),
        [ pageId ]
    );
 
    const { saveEditedEntityRecord, editEntityRecord } = useDispatch( coreDataStore );
    const handleSave = async () => {
        const savedRecord = await saveEditedEntityRecord( 'postType', 'page', pageId );
        if ( savedRecord ) {
            onSaveFinished();
        }
    };
    const handleChange = ( title ) =>  editEntityRecord( 'postType', 'page', page.id, { title } );
 
    return (
        <PageForm
            title={ page.title }
            onChangeTitle={ handleChange }
            hasEdits={ hasEdits }
            lastError={ lastError }
            isSaving={ isSaving }
            onCancel={ onCancel }
            onSave={ handleSave }
        />
    );
}

// Adds the UI for the edit form
export function PageForm( { title, onChangeTitle, hasEdits, lastError, isSaving, onCancel, onSave } ) {
    return (
        <div className="my-gutenberg-form">
            <TextControl
                label="Page title:"
                value={ title }
                onChange={ onChangeTitle }
            />
            { lastError ? (
                <div className="form-error">Error: { lastError.message }</div>
            ) : (
                false
            ) }
            <div className="form-buttons">
                <Button
                    onClick={ onSave }
                    variant="primary"
                    disabled={ !hasEdits || isSaving }
                >
                    { isSaving ? (
                        <>
                            <Spinner/>
                            Saving
                        </>
                    ) : 'Save' }
                </Button>
                <Button
                    onClick={ onCancel }
                    variant="tertiary"
                    disabled={ isSaving }
                >
                    Cancel
                </Button>
            </div>
        </div>
    );
}

export function VanillaReactForm({ initialTitle }) {
    const [title, setTitle] = useState( initialTitle );
    return (
        <TextControl
            value={ title }
            onChange={ setTitle }
        />
    );
}

// grant the ability to create a new page with this plugin
function CreatePageButton() {
    const [isOpen, setOpen] = useState( false );
    const openModal = () => setOpen( true );
    const closeModal = () => setOpen( false );
    return (
        <>
            <Button onClick={ openModal } variant="primary">
                Create a new Page
            </Button>
            { isOpen && (
                <Modal onRequestClose={ closeModal } title="Create a new page">
                    <CreatePageForm
                        onCancel={ closeModal }
                        onSaveFinished={ closeModal }
                    />
                </Modal>
            ) }
        </>
    );
}
 
export function CreatePageForm( { onCancel, onSaveFinished } ) {
    const [title, setTitle] = useState();
    const handleChange = ( title ) => setTitle( title );
    const { saveEntityRecord } = useDispatch( coreDataStore );
    const { lastError, isSaving } = useSelect(
        ( select ) => ( {
            // Notice the missing pageId argument:
            lastError: select( coreDataStore )
                .getLastEntitySaveError( 'postType', 'page' ),
            // Notice the missing pageId argument
            isSaving: select( coreDataStore )
                .isSavingEntityRecord( 'postType', 'page' ),
        } ),
        []
    );
    const handleSave = async () => {
        const savedRecord = await saveEntityRecord(
            'postType',
            'page',
            { title, status: 'publish' }
        );
        if ( savedRecord ) {
            onSaveFinished();
        }
    };
    return (
        <PageForm
            title={ title }
            onChangeTitle={ setTitle }
            hasEdits={ !!title }
            onSave={ handleSave }
            onCancel={ onCancel }
            lastError={ lastError }
            isSaving={ isSaving }
        />
    );
}

function PageEditButton({ pageId }) {
    const [ isOpen, setOpen ] = useState( false );
    const openModal = () => setOpen( true );
    const closeModal = () => setOpen( false );
    return (
        <>
            <Button onClick={ openModal } variant="primary">
                Edit
            </Button>
            { isOpen && (
                <Modal onRequestClose={ closeModal } title="Edit page">
                    <EditPageForm
                        pageId={ pageId }
                        onCancel={ closeModal }
                        onSaveFinished={ closeModal }
                    />
                </Modal>
            ) }
        </>
    );
}

// Adds the searchbar, and page list
function MyFirstApp() {
    const [ searchTerm, setSearchTerm ] = useState( '' );
    const { pages, hasResolved } = useSelect(
        ( select ) => {
            const query = {};
            if ( searchTerm ) {
                query.search = searchTerm;
            }
            const selectorArgs = [ 'postType', 'page', query ];
            return {
                pages: select( coreDataStore ).getEntityRecords(
                    ...selectorArgs
                ),
                hasResolved: select( coreDataStore ).hasFinishedResolution(
                    'getEntityRecords',
                    selectorArgs
                ),
            };
        },
        [ searchTerm ]
    );
  
    return (
        <div>
            <div className="list-controls">
                <SearchControl onChange={ setSearchTerm } value={ searchTerm }/>
                <CreatePageButton/>
            </div>
            <PagesList hasResolved={ hasResolved } pages={ pages }/>
            <Notifications />
        </div>
    );
}

window.addEventListener(
    'load',
    function () {
        render(
            <MyFirstApp />,
            document.querySelector( '#my-first-gutenberg-app' )
        );
    },
    false
);

// adds the notification system
function Notifications() {
    const notices = useSelect(
        ( select ) => select( noticesStore ).getNotices(),
        []
    );
    const { removeNotice } = useDispatch( noticesStore );
    const snackbarNotices = notices.filter( ({ type }) => type === 'snackbar' );
 
    return (
        <SnackbarList
            notices={ snackbarNotices }
            className="components-editor-notices__snackbar"
            onRemove={ removeNotice }
        />
    );
}

// adds a delete button to the pages list
function DeletePageButton ({ pageId }) {
    const { deleteEntityRecord } = useDispatch( coreDataStore );
    const { createSuccessNotice, createErrorNotice } = useDispatch( noticesStore );
    // useSelect returns a list of selectors if you pass the store handle
    // instead of a callback:
    const { getLastEntityDeleteError } = useSelect( coreDataStore )
 
    const handleDelete = async () => {
        const success = await deleteEntityRecord( 'postType', 'page', pageId);
        if ( success ) {
            // Tell the user the delete succeeded
            createSuccessNotice( "The page was deleted!", {
                type: 'snackbar',
            } );
        } else {
            // We use the selector directly to get the fresh error *after* the deleteEntityRecord
            // have failed.
            const lastError = getLastEntityDeleteError( 'postType', 'page', pageId );
            const message = ( lastError?.message || 'There was an error.' ) + ' Please refresh the page and try again.'
            // Tell the user how exactly the delete has failed
            createErrorNotice( message, {
                type: 'snackbar',
            } );
        }
    }
    const { isDeleting } = useSelect(
        select => ({
            isDeleting: select( coreDataStore ).isDeletingEntityRecord( 'postType', 'page', pageId ),
        }),
        [ pageId ]
    )
    
    return (
        <Button variant="primary" onClick={ handleDelete } disabled={ isDeleting }>
            { isDeleting ? (
                <>
                    <Spinner />
                    Deleting...
                </>
            ) : 'Delete' }
        </Button>
    );
}

// loads the list of pages
function PagesList( { hasResolved, pages } ) {
    if ( !hasResolved ) {
        return <Spinner/>
    }
    if ( !pages?.length ) {
        return <div>No results</div>
    }
    return (
        <table className="wp-list-table widefat fixed striped table-view-list">
            <thead>
                <tr>
                    <td>Title</td>
                </tr>
            </thead>
            <tbody>
                { pages?.map( ( page ) => (
                    <tr key={ page.id }>
                        <td>{ decodeEntities( page.title.rendered ) }</td>
                        <td>
                            <PageEditButton pageId={ page.id } />
                            <DeletePageButton pageId={ page.id }/>
                        </td>
                    </tr>
                ) ) }
            </tbody>
        </table>
    );
}
