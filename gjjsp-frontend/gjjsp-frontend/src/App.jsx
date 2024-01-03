import Login from './pages/LoginPage/Login'
import Dashboard from './pages/ClientPages/Dashboard/Dashboard';
import Notification from './pages/ClientPages/Notification';
import User from './pages/ClientPages/User';
import Scholar from './pages/ClientPages/Scholar';
import Categories from './pages/ClientPages/Categories';
import Submission from './pages/ClientPages/Submission/Submission';
import Export from './pages/ClientPages/Export';
import Create from './pages/ClientPages/Create/Create';
import Ask from './pages/GeneralPages/AskAI/Ask';
import RequireAuth from './pages/ClientPages/RequireAuth';
import {Routes, Route} from 'react-router-dom'; 
import ViewSubmission from './pages/ClientPages/Submission/ViewSubmission';
import NotFound from './pages/GeneralPages/NotFound/NotFound';
import Unauthorized from './pages/GeneralPages/Unauthorized/Unauthorized';
import Partner from './pages/ClientPages/Partner';
import ScholarDashboard from './pages/ScholarPages/ScholarDashboard/ScholarDashboard';
import ScholarSubmission from './pages/ScholarPages/ScholarSubmission/ScholarSubmission';
import ScholarProfile from './pages/ScholarPages/ScholarProfile/ScholarProfile';
import Profile from './pages/ClientPages/Profile';
import ForgotPassword from './pages/GeneralPages/ForgotPassword';

const ROLES = {
  'Admin': 1,
  'Manager': 2,
  'Scholar': 3
}

function App() {
  return (
    <Routes>
      {/* Public Route */}
      <Route path="login" element={<Login/>}/>
      <Route path='*' element={<NotFound/>}/>
      <Route path='unauthorized' element={<Unauthorized/>}/>
      <Route path='forgot-password' element={<ForgotPassword/>}/>



    {/* Protected Route */}
    
      <Route element={<RequireAuth allowedRoles={[ROLES.Admin]}/>}> 
        <Route path="user" element={<User/>}/>
        <Route path="view" element={<ViewSubmission/>}/>
      </Route>

      {/* Scholar Route */}
      <Route element={<RequireAuth allowedRoles={[ROLES.Scholar]}/>}>
        <Route path="scholar-dashboard" element={<ScholarDashboard/>}/>
        <Route path="scholar-submission" element={<ScholarSubmission/>}/>
        <Route path="scholar-profile" element={<ScholarProfile/>}/>
      </Route>

      {/* Admin and Manager Route */}
      <Route element={ <RequireAuth allowedRoles={[ROLES.Admin, ROLES.Manager]}/>}> 
        <Route path="/" element={<Dashboard/>}/>
        <Route path="scholar" element={<Scholar/>}/>
        <Route path="categories" element={<Categories/>}/>
        <Route path="export" element={<Export/>}/>
        <Route path='partner' element={<Partner/>}/>
        <Route path="submission" element={<Submission/>}/>
        <Route path="profile" element={<Profile/>}/>
        <Route path="notification" element={<Notification/>}/>
      </Route>

      {/* All Routes */}
      <Route element={<RequireAuth allowedRoles={[ROLES.Admin, ROLES.Manager, ROLES.Scholar]}/>}> 
        <Route path="create" element={<Create/>}/>
        <Route path="ask" element={<Ask/>}/>
      </Route>
    </Routes>


  )
}

export default App
