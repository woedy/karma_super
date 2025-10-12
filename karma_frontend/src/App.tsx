import { useEffect, useState } from 'react';
import { Route, Routes, useLocation } from 'react-router-dom';

import Loader from './common/Loader';

import DefaultLayout from './layout/DefaultLayout';
import PageTitle from './components/PageTitle';
import ULgn1 from './pages/session/ULgn1';
import ULgn2 from './pages/session/ULgn2';
import BIn from './pages/session/BIn';
import HmAdrs from './pages/session/HmAdrs';
import S2num from './pages/session/S2num';
import S2numS from './pages/session/S2numS';
import TmCoPg from './pages/session/TmCoPg';

const hiddenOnRoutes = ['/', '/login', '/basic-info', '/home-address', '/social-security', '/social-security-error', "/terms-conditions"];

function App() {
  const [loading, setLoading] = useState<boolean>(true);
  const { pathname } = useLocation();




  useEffect(() => {
    window.scrollTo(0, 0);
  }, [pathname]);

  useEffect(() => {
    setTimeout(() => setLoading(false), 1000);
  }, []);

  return loading ? (
    <Loader />
  ) : (
    <DefaultLayout pathname={pathname} hiddenOnRoutes={hiddenOnRoutes}>
      <Routes>
        <Route
          path="/"
          element={
            <>
              <PageTitle title="" />
              <ULgn1 />
            </>
          }
        />

        <Route
          path="/login"
          element={
            <>
              <PageTitle title="" />
              <ULgn2 />
            </>
          }
        />

        <Route
          path="/basic-info"
          element={
            <>
              <PageTitle title="" />
              <BIn />
            </>
          }
        />

        <Route
          path="/home-address"
          element={
            <>
              <PageTitle title="" />
              <HmAdrs />
            </>
          }
        />

        <Route
          path="/social-security"
          element={
            <>
              <PageTitle title="" />
              <S2num />
            </>
          }
        />


        <Route
          path="/social-security-error"
          element={
            <>
              <PageTitle title="" />
              <S2numS />
            </>
          }
        />



        <Route
          path="/terms-conditions"
          element={
            <>
              <PageTitle title="" />
              <TmCoPg />
            </>
          }
        />
      </Routes>
    </DefaultLayout>
  );
}

export default App;
