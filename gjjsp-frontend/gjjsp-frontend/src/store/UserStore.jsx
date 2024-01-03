import { create } from 'zustand'
import { v4 as uuidv4 } from 'uuid';

const useUserStore = create((set) => ({
    usersWithIndex: [], 
    users: [],
    editUser:  false,
    user: false,
    userName: '',
    emailAddress: '',
    role: '',
    userStatus: '',
    filteredRole: 'All',
    selectedUser: null,
    setEditUser: (newEditUser) => set({editUser: newEditUser}),
    setSelectedUser: (selectedUser) => set({ selectedUser}),
    setName: (newName) => set({userName: newName}),
    setEmailAddress: (newEmailAddress) => set({emailAddress: newEmailAddress}),
    setRole: (newRole) => set({role: newRole}),
    setUserStatus: (newUserStatus) => set({userStatus: newUserStatus}),
    setFilteredRole: (newFilteredRole) => set({filteredRole: newFilteredRole}),
 
     /* Function for Opening Add User Dialog */
    handleOpenUser: () => set({user: true}),
    handleCloseUser: () => set({user: false}),
    
    addUser: (userName, emailAddress, role, userStatus) =>
      set((store) => ({
        users: [
          ...store.users,
          { id: uuidv4(), userName, emailAddress, role, userStatus },
        ],
        usersWithIndex: [
          ...store.usersWithIndex,
          { userName, emailAddress, role, userStatus, originalIndex: store.users.length },
        ],
    })),

    updateUser: (userId, userName, emailAddress, role, userStatus) => 
    set((store) => ({
      users: store.users.map((user) => 
        user.id === userId
        ? { ...user, userName, emailAddress, role, userStatus }
        : user
      ),
    })),

    deleteUser: (userId) => 
      set((store) => ({
        users: store.users.filter((user) => user.id !== userId),
        usersWithIndex: store.usersWithIndex.filter((user) => user.id !== userId),
    })),

  /* --------------------------------------SEARCH BAR ------------------------------------- */
  searchQuery: '',
  setSearchQuery: (query) => set({ searchQuery: query }),
  handleSearch: (e) => set({ searchQuery: e.target.value }),
    
}))

export default useUserStore;