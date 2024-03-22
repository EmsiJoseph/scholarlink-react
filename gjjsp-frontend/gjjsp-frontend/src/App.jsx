import React, {lazy, Suspense} from 'react';
import RequireAuth from './pages/ClientPages/RequireAuth';
import {Routes, Route} from 'react-router-dom'; 

//Public Imports
const LazyLogin = lazy(() => import('./pages/LoginPage/Login'));
const LazyForgotPassword = lazy(() => import('./pages/GeneralPages/ForgotPassword'));
const LazyNotFound = lazy(() => import('./pages/GeneralPages/NotFound/NotFound'));
const LazyUnauthorized = lazy(() => import('./pages/GeneralPages/Unauthorized/Unauthorized'));

//All User Imports
const LazyCreate = lazy(() => import('./pages/ClientPages/Create'));
const LazyGenerate = lazy(() => import('./pages/ClientPages/GenerateReport/GenerateReport'));

//Admin & Manager Imports
const LazyDashboard = lazy(() => import('./pages/ClientPages/Dashboard'));
const LazyNotification = lazy(() => import('./pages/ClientPages/Notification'));
const LazyUser = lazy(() => import('./pages/ClientPages/User'));
const LazyScholar = lazy(() => import('./pages/ClientPages/Scholar'));
const LazyCategories = lazy(() => import('./pages/ClientPages/Categories'));
const LazySubmission = lazy(() => import('./pages/ClientPages/Submission'));
const LazyExport = lazy(() => import('./pages/ClientPages/Export'));
const LazyPartner = lazy(() => import('./pages/ClientPages/Partner'));
const LazyProfile = lazy(() => import('./pages/ClientPages/Profile'));
const LazyRenewalViewSubmission = lazy(() => import('./pages/ClientPages/RenewalViewSubmission'));
const LazyGraduatingViewSubmission = lazy(() => import('./pages/ClientPages/GraduatingViewSubmission'));
const LazyAlumniViewSubmission = lazy(() => import('./pages/ClientPages/AlumniViewSubmission'));
const LazyClientSubmissionRenewal = lazy(() => import('./pages/ClientPages/RenewalSubmitted'));
const LazyClientSubmissionGraduating = lazy(() => import('./pages/ClientPages/GraduatingSubmitted'));
const LazyClientSubmissionAlumni = lazy(() => import('./pages/ClientPages/AlumniSubmitted'));

//Scholar Imports
const LazyScholarDashboard = lazy(() => import('./pages/ScholarPages/ScholarDashboard/ScholarDashboard'));
const LazyScholarProfile = lazy(() => import('./pages/ScholarPages/ScholarProfile/ScholarProfile'));
const LazyScholarSubmissionRenewal = lazy(() => import('./pages/ScholarPages/ScholarSubmission/RenewalSubmission'));
const LazyScholarSubmissionGraduating = lazy(() => import('./pages/ScholarPages/ScholarSubmission/GraduatingSubmission'));
const LazySholarSubmissionAlumni = lazy(() => import('./pages/ScholarPages/ScholarSubmission/AlumniSubmission'));

const ROLES = {
  'Admin': 1,
  'Manager': 2,
  'Scholar': 3
}

function App() {
  return (
    <Routes>
      {/* Public Route */}

      <Route 
        path="login" 
        element={
        <Suspense fallback="Scholarlink Loading...">
          <LazyLogin/>
        </Suspense>}
      />

      <Route 
        path='*' 
        element={
        <Suspense fallback="Scholarlink Loading...">
          <LazyNotFound/>
        </Suspense>
        }
      />

      <Route 
        path='unauthorized' 
        element={
        <Suspense fallback="Scholarlink Loading...">
          <LazyUnauthorized/>
        </Suspense>
        }
      />
      <Route 
        path='forgot-password' 
        element={
        <Suspense fallback="Scholarlink Loading...">
          <LazyForgotPassword/>
        </Suspense>
        }
      />



    {/* Protected Route */}
    
      <Route element={<RequireAuth allowedRoles={[ROLES.Admin]}/>}> 
        <Route path="user" 
          element={
          <Suspense fallback="Scholarlink Loading...">
            <LazyUser/>
          </Suspense>
          }
        />

      </Route>

      {/* Scholar Route */}
      <Route element={<RequireAuth allowedRoles={[ROLES.Scholar]}/>}>

        <Route 
          path="scholar-dashboard" 
          element={
            <Suspense fallback="Scholarlink Loading...">
              <LazyScholarDashboard/>
            </Suspense>
          }
        />

        <Route
          path="submission-renewal"
          element={
            <Suspense fallback="Scholarlink Loading...">
              <LazyScholarSubmissionRenewal/>
            </Suspense>
          }
        />

        <Route
          path="submission-graduating"
          element={
            <Suspense fallback="Scholarlink Loading...">
              <LazyScholarSubmissionGraduating/>
            </Suspense>
          }
        />

        <Route
          path="submission-alumni"
          element={
            <Suspense fallback="Scholarlink Loading...">
              <LazySholarSubmissionAlumni/>
            </Suspense>
          }
        />

      </Route>


      {/* Admin and Manager Route */}
      <Route element={ <RequireAuth allowedRoles={[ROLES.Admin, ROLES.Manager]}/>}> 
        <Route 
          path="/" 
          element={
          <Suspense fallback="Scholarlink Loading...">
            <LazyDashboard/>
          </Suspense>
          }
        />

        <Route 
          path="generate-report" 
          element={
          <Suspense fallback="Scholarlink Loading...">
            <LazyGenerate/>
          </Suspense>
          }
        />
      
        <Route 
          path="notification" 
          element={
          <Suspense fallback="Scholarlink Loading...">
            <LazyNotification/>
          </Suspense>
          }
        />


        <Route 
          path="scholar" 
          element={
          <Suspense fallback="Scholarlink Loading...">
            <LazyScholar/>
          </Suspense>
          }
        />

        <Route 
          path="categories" 
          element={
          <Suspense fallback="Scholarlink Loading...">
            <LazyCategories/>
          </Suspense>
          }
        />

        <Route 
          path="export" 
          element={
          <Suspense fallback="Scholarlink Loading...">
            <LazyExport/>
          </Suspense>
          }
        />

        <Route 
          path='partner' 
          element={
          <Suspense fallback="Scholarlink Loading...">
            <LazyPartner/>
          </Suspense>
          }
        />

        <Route 
          path="submission" 
          element={
          <Suspense fallback="Scholarlink Loading...">
            <LazySubmission/>
          </Suspense>
          }
        />

        <Route 
          path="profile" 
          element={
          <Suspense fallback="Scholarlink Loading...">
            <LazyProfile/>
          </Suspense>
          }
        />
      
      <Route 
          path="renewal-view" 
          element={
          <Suspense fallback="Scholarlink Loading...">
            <LazyRenewalViewSubmission/>
          </Suspense>
          }
      />

      <Route 
          path="graduating-view" 
          element={
          <Suspense fallback="Scholarlink Loading...">
            <LazyGraduatingViewSubmission/>
          </Suspense>
          }
      />

      <Route 
          path="alumni-view" 
          element={
          <Suspense fallback="Scholarlink Loading...">
            <LazyAlumniViewSubmission/>
          </Suspense>
          }
      />
      
      <Route
        path="submitted-renewal"
        element={
          <Suspense fallback="Scholarlink Loading...">
            <LazyClientSubmissionRenewal/>
          </Suspense>
        }
      />

      <Route
        path="submitted-graduating"
        element={
          <Suspense fallback="Scholarlink Loading...">
            <LazyClientSubmissionGraduating/>
          </Suspense>
        }
      />

      <Route
        path="submitted-alumni"
        element={
          <Suspense fallback="Scholarlink Loading...">
            <LazyClientSubmissionAlumni/>
          </Suspense>
        }
      />


      </Route>

      {/* All Routes */}
      <Route element={<RequireAuth allowedRoles={[ROLES.Admin, ROLES.Manager, ROLES.Scholar]}/>}>


        <Route 
          path="create" 
          element={
          <Suspense fallback="Scholarlink Loading...">
            <LazyCreate/>
          </Suspense>
          }
        />

<Route 
          path="scholar-profile" 
          element={
            <Suspense fallback="Scholarlink Loading...">
              <LazyScholarProfile/>
            </Suspense>
          }
        />
        
      </Route>
    </Routes>


  )
}

export default App
