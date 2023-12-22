import React from 'react'
import * as MUI from '../../import';
import Layout from '../Components/Layout';
import { Search, SearchIconWrapperV2,StyledInputBaseV2 } from '../Components/Styles';
import useUserStore from '../Store/UserStore';
import {useForm, Controller } from 'react-hook-form';
import { DevTool } from "@hookform/devtools";

const USER_REGEX = /^[A-Za-z.-]+(\s*[A-Za-z.-]+)*$/;
const EMAIL_REGEX =  /^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;

const FormValues = {
  userName: "",
  emailAddress: "",
  role: "",
}

export default function User({state}) {

  const form  = useForm();
  const { register, control, handleSubmit, formState, reset, validate} = form
  const { errors } = formState;

  const onSubmit = (data) => {
    console.log('Form submitted',data);  

    if(editUser) {
      updateUser(selectedUser.id, data.userName, data.emailAddress, data.role);
      setEditUser(false);
    }
    else{
      addUser(data.userName, data.emailAddress, data.role);
    }
    form.reset();
    handleCloseUser();
    
  }

  const {
    user,
    handleOpenUser,
    handleCloseUser,
    filteredRole,
    setFilteredRole,
    editUser,
    setEditUser,
    updateUser,
    searchQuery,
    handleSearch,
    selectedUser,
    setSelectedUser,
    addUser = ((store) => store.addUser),
    deleteUser = ((store) => store.deleteRow),
    users = ((store) => store.users.filter((user) => user.state === state)),
  } = useUserStore();
  
  const handleEditUser = (userId) => {
    const selectedUser = users.find((user) => user.id === userId);
    if (selectedUser) {
      setSelectedUser(selectedUser);
      reset({ // Reset form and pre-fill fields for editing
        userName: selectedUser.userName,
        emailAddress: selectedUser.emailAddress,
        role: selectedUser.role,
      });
      setEditUser(true);
      handleOpenUser();
    }
  };

  const handleDeleteUser = (userId) => {
    const selectedUser = users.find((user) => user.id === userId);
    if (selectedUser) {
      deleteUser(selectedUser.id);
    }
  }

  const handleCancelUser = () => {
    form.reset(FormValues); // Reset the form fields
    setEditUser(false);
    handleCloseUser(); // Close the dialog
  }
  
  return (
  <Layout>
    <MUI.Container maxWidth="lg" sx={{ mt: 4, mb: 4 }}>
      <MUI.Grid container spacing={3}>
      
        <MUI.Grid item xs={12}>
          <MUI.Box display="flex" flexDirection={{ xs: 'column', sm: 'row' }} alignItems={{xs: 'left', md: 'center'}} margin={2} justifyContent="space-between">
            <MUI.Typography variant="h1" id="tabsTitle">Users</MUI.Typography>
                      
              {/* Add User Button */}
              <MUI.Button variant="contained" color="primary" id='addButton' sx={{width: {xs: '100px'}}} onClick={handleOpenUser}>
                Add Users 
              </MUI.Button>

            </MUI.Box>
        </MUI.Grid>

        <MUI.Container sx={{mt: 4, display: 'flex', alignItems: 'center' }}>
          <Search>
            <SearchIconWrapperV2>
              <MUI.SearchIcon />
            </SearchIconWrapperV2>
            <StyledInputBaseV2
              placeholder="Search for names, groups, or email addresses"
              inputProps={{ 'aria-label': 'search' }}
              value={searchQuery}
              onChange={handleSearch} 
            />
          </Search>
                          
          <MUI.IconButton aria-label="filter">
            <MUI.FilterListIcon />
          </MUI.IconButton>

          <MUI.FormControl id="filterControl">
            <MUI.Select
              value={filteredRole}
              onChange={(e) => setFilteredRole(e.target.value)} 
              native
            >
              <option value="All">All</option>
              <option value="Administrator">Scholarship Administrator</option>
              <option value="Scholar Manager">Scholar Manager</option>
              <option value="Scholar">Scholar</option>
            </MUI.Select>
          </MUI.FormControl>
          </MUI.Container>
          
          {/* -------- Table Section  ----------*/}
          <MUI.TableContainer sx={{ backgroundColor: '#fbf3f2', margin: '2rem 0 0 1rem' }}>
            <MUI.Table> 
              <MUI.TableHead>
                <MUI.TableRow>
                  <MUI.TableCell>Name</MUI.TableCell>
                  <MUI.TableCell>Email</MUI.TableCell>
                  <MUI.TableCell>Role</MUI.TableCell>
                  <MUI.TableCell sx={{marginLeft: 100}}>Action</MUI.TableCell>
                </MUI.TableRow>
              </MUI.TableHead>
                <MUI.TableBody>
                  {users
                    .filter((user) => filteredRole === "All" || user.role === filteredRole)
                    .filter((user) => 
                    (user.userName && user.userName.toLowerCase().includes(searchQuery?.toLowerCase())) ||
                    (user.emailAddress && user.emailAddress.toLowerCase().includes(searchQuery?.toLowerCase()))
                    )
                    .reverse()
                    .map((user) => (
                    (user.userName || user.emailAddress || user.role) && (
                    <MUI.TableRow key={user.id} className='user' >
                      <MUI.TableCell sx={{border: 'none'}}  className='name'>{user.userName}</MUI.TableCell>
                      <MUI.TableCell sx={{border: 'none'}}  className='email'>{user.emailAddress}</MUI.TableCell>
                      <MUI.TableCell sx={{border: 'none'}}  className='role'>{user.role}</MUI.TableCell>
                      <MUI.TableCell sx={{border: 'none', }}>
                        <MUI.IconButton
                          color="inherit"
                          onClick={() => handleEditUser(user.id)}
                          sx={{ marginLeft: -2 }}
                        >
                          <MUI.BorderColorOutlinedIcon />

                        </MUI.IconButton>

                        <MUI.IconButton
                          color="inherit"
                          onClick={() => handleDeleteUser(user.id)}
                          sx={{ textTransform: 'capitalize' }}
                        >
                          <MUI.DeleteOutlineOutlinedIcon />

                        </MUI.IconButton>

                      </MUI.TableCell>
                    </MUI.TableRow>
                  )))}
                </MUI.TableBody>
            </MUI.Table>
            <MUI.Divider sx={{width:'100%'}}/>
          </MUI.TableContainer>   

          {/* ------------------ Dialog Box of the  Users ---------------*/ }

          {/* Add User Dialog */}
          <MUI.Dialog open={user} onClose={handleCloseUser} fullWidth maxWidth="xs" component='form' onSubmit={handleSubmit(onSubmit)} noValidate>
            {/* Content of the Dialog */}
            <MUI.DialogTitle id="dialogTitle">New Users</MUI.DialogTitle>
            <MUI.Typography variant='body2' id="dialogLabel">Required fields are marked with an asterisk *</MUI.Typography>
              <MUI.DialogContent>
                {/* Form Fields of New User*/}
                <MUI.Grid id="userNameGrid">
                  <MUI.InputLabel htmlFor="userName" id="userNameLabel">Name</MUI.InputLabel>
                    <MUI.TextField 
                      type='text'
                      id='userName'
                      placeholder='Name' 
                      fullWidth 
                      
                      {...register("userName", {
                        required: {
                          value: true,
                          message: 'Full name is required',
                        },
                        pattern: {
                          value: USER_REGEX,
                          message: 'Names should only contain letters, periods, and hypens, with no leading or hanging spaces.',
                        }
                      })}
                    />
                    {errors.userName && (
                     <p id='errMsg'> 
                      <MUI.InfoIcon className='infoErr'/> 
                      {errors.userName?.message}  
                     </p>
                    )}
                </MUI.Grid>

                <MUI.Grid id="emailAddressGrid">
                  <MUI.InputLabel htmlFor="emailAddress" id="emailAddressLabel">Email</MUI.InputLabel>
                  <MUI.TextField 
                    type='email'
                    id='emailAddress'
                    placeholder='Email Address' 
                    fullWidth 
                    {...register("emailAddress", {
                      required: {
                        value: true,
                        message: 'Email Address is required',
                      },
                      pattern: {
                        value: EMAIL_REGEX,
                        message: 'Please enter a valid email address',
                      }
                    })}
                  />
                  {errors.emailAddress && (
                    <p id='errMsg'> <MUI.InfoIcon className='infoErr'/> {errors.emailAddress?.message}</p>
                  )}
                </MUI.Grid>

                <MUI.Grid id="roleGrid">
                  <MUI.InputLabel htmlFor="role" id='roleLabel'>Role</MUI.InputLabel>
                  <Controller
                    name="role"
                    control={control}
                    defaultValue=''
                    rules={{ 
                      required: 'Role is required', 
                      validate: (value) => value !== '' || 'Please select a role' 
                    }}
                    render={({ field }) => (
                      
                      <MUI.FormControl sx={{ width: '100%', borderRadius: '8px' }}>
                        <MUI.Select
                          id='role'
                          native
                          {...field}
                        >
                          <option value="" disabled>Select Role</option>
                          <option value="Administrator">Scholarship Administrator</option>
                          <option value="Scholar Manager">Scholar Manager</option>
                          <option value="Scholar">Scholar</option>
                        </MUI.Select>
                      </MUI.FormControl>
                    )}
                  />
                  {errors.role && (
                    <p id='errMsg'> <MUI.InfoIcon className='infoErr'/> {errors.role?.message}</p>
                  )} 
                </MUI.Grid>

              </MUI.DialogContent>

              <MUI.DialogActions>
                {/* Add action buttons, e.g., Save Changes and Cancel */}
                <MUI.Button onClick={handleCancelUser} color="primary" id='Button'>
                  Cancel
                </MUI.Button>
                  <MUI.Button
                    color="primary" 
                    type='submit' 
                    variant='contained'
                    id='Button'
                    >
                    {editUser ? 'Save Changes' : 'Add user'}
                  </MUI.Button>
              </MUI.DialogActions>
          </MUI.Dialog>

          <DevTool control={control} />
      </MUI.Grid>
    </MUI.Container>
  </Layout>
  )
}
